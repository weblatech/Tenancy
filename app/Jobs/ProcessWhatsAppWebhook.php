<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 15;

    /**
     * @var string  Tenant/store ID (extracted from webhook URL)
     */
    protected string $storeId;

    /**
     * @var array  Raw webhook payload from Meta
     */
    protected array $payload;

    /**
     * Create a new job instance.
     */
    public function __construct(string $storeId, array $payload)
    {
        $this->storeId = $storeId;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * Flow:
     * 1. Parse the webhook payload
     * 2. For button clicks (confirm_X / cancel_X): update order status + send follow-up
     * 3. For text messages: store in whatsapp_messages for chat panel
     * 4. Log all actions to whatsapp_logs
     */
    public function handle(): void
    {
        // Initialize tenancy for this store
        if (!tenancy()->initialized) {
            $tenant = \App\Models\Tenant::find($this->storeId);
            if ($tenant) {
                tenancy()->initialize($tenant);
            } else {
                Log::error('ProcessWhatsAppWebhook: Tenant not found', ['store_id' => $this->storeId]);
                return;
            }
        }

        $service = new WhatsAppService($this->storeId);

        try {
            $entry = $this->payload['entry'][0] ?? null;
            if (!$entry) return;

            $changes = $entry['changes'][0] ?? null;
            if (!$changes) return;

            $value = $changes['value'] ?? [];
            $messages = $value['messages'] ?? [];

            foreach ($messages as $message) {
                $from = $message['from'] ?? '';
                $messageId = $message['id'] ?? '';
                $timestamp = $message['timestamp'] ?? '';

                // ── Interactive button clicks ──
                if (($message['type'] ?? '') === 'interactive') {
                    $this->handleButtonClick($message, $from, $service);
                    continue;
                }

                // ── Text messages ──
                if (($message['type'] ?? '') === 'text') {
                    $text = $message['text']['body'] ?? '';
                    $this->handleTextMessage($from, $text, $messageId, $service);
                    continue;
                }

                // ── Image / Document / Audio / Video ──
                if (in_array($message['type'] ?? '', ['image', 'document', 'audio', 'video'])) {
                    $this->handleMediaMessage($from, $message, $service);
                    continue;
                }
            }

            // ── Status updates (delivered, read, etc.) ──
            $statuses = $value['statuses'] ?? [];
            foreach ($statuses as $status) {
                $this->handleStatusUpdate($status);
            }

        } catch (\Exception $e) {
            Log::error('ProcessWhatsAppWebhook failed', [
                'store_id' => $this->storeId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle interactive button click (Confirm / Cancel order)
     */
    protected function handleButtonClick(array $message, string $from, WhatsAppService $service): void
    {
        $buttonId = $message['interactive']['button']['id'] ?? '';
        $buttonTitle = $message['interactive']['button']['title'] ?? '';

        // Pattern: confirm_123 or cancel_123
        if (!preg_match('/^(confirm|cancel)_(\d+)$/', $buttonId, $matches)) {
            Log::info('Unknown button clicked', ['button_id' => $buttonId, 'store_id' => $this->storeId]);
            return;
        }

        $action = $matches[1];
        $orderId = (int) $matches[2];

        $order = Order::find($orderId);
        if (!$order) {
            Log::warning('Button click: Order not found', ['order_id' => $orderId]);
            return;
        }

        // Determine new status
        $newStatus = $action === 'confirm' ? 'processing' : 'cancelled';

        // Update order status
        $order->update(['status' => $newStatus]);

        // Log the customer action
        $this->logInboundAction($from, "Customer clicked: {$buttonTitle}", $orderId);

        // Send follow-up notification
        if ($action === 'confirm') {
            SendWhatsAppMessage::dispatch($this->storeId, 'order_confirmed', $service->formatPhone($order->customer_phone), null, $orderId);
            Log::info('Order confirmed via WhatsApp', ['order_id' => $orderId, 'store_id' => $this->storeId]);
        } else {
            SendWhatsAppMessage::dispatch($this->storeId, 'order_cancelled', $service->formatPhone($order->customer_phone), null, $orderId);
            Log::info('Order cancelled via WhatsApp', ['order_id' => $orderId, 'store_id' => $this->storeId]);
        }
    }

    /**
     * Handle incoming text message
     */
    protected function handleTextMessage(string $from, string $text, string $messageId, WhatsAppService $service): void
    {
        $formattedPhone = $service->formatPhone($from);

        // Find existing conversation by phone
        $conversation = \DB::table('whatsapp_conversations')
            ->where('tenant_id', $this->storeId)
            ->where('customer_phone', $formattedPhone)
            ->orderByDesc('last_message_at')
            ->first();

        if ($conversation) {
            // Store in whatsapp_messages
            \DB::table('whatsapp_messages')->insert([
                'conversation_id' => $conversation->id,
                'tenant_id' => $this->storeId,
                'direction' => 'inbound',
                'message_type' => 'text',
                'message_body' => $text,
                'from_phone' => $formattedPhone,
                'to_phone' => $service->getPhoneNumberId(),
                'status' => 'received',
                'provider_message_id' => $messageId,
                'is_auto' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update conversation
            \DB::table('whatsapp_conversations')
                ->where('id', $conversation->id)
                ->update([
                    'last_message_at' => now(),
                    'unread_count' => \DB::raw('unread_count + 1'),
                ]);

            // Log to whatsapp_logs
            $this->logInboundAction($from, $text, $conversation->order_id ?? 0);
        } else {
            Log::info('Incoming message from unknown phone', [
                'phone' => $formattedPhone,
                'store_id' => $this->storeId,
            ]);
        }
    }

    /**
     * Handle incoming media message (image, document, etc.)
     */
    protected function handleMediaMessage(string $from, array $message, WhatsAppService $service): void
    {
        $formattedPhone = $service->formatPhone($from);
        $type = $message['type'] ?? 'unknown';

        $conversation = \DB::table('whatsapp_conversations')
            ->where('tenant_id', $this->storeId)
            ->where('customer_phone', $formattedPhone)
            ->orderByDesc('last_message_at')
            ->first();

        if ($conversation) {
            \DB::table('whatsapp_messages')->insert([
                'conversation_id' => $conversation->id,
                'tenant_id' => $this->storeId,
                'direction' => 'inbound',
                'message_type' => $type,
                'message_body' => "[{$type}] Incoming media message",
                'from_phone' => $formattedPhone,
                'to_phone' => $service->getPhoneNumberId(),
                'status' => 'received',
                'provider_message_id' => $message['id'] ?? null,
                'is_auto' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \DB::table('whatsapp_conversations')
                ->where('id', $conversation->id)
                ->update([
                    'last_message_at' => now(),
                    'unread_count' => \DB::raw('unread_count + 1'),
                ]);
        }
    }

    /**
     * Handle message status updates (sent → delivered → read)
     */
    protected function handleStatusUpdate(array $status): void
    {
        $providerMessageId = $status['id'] ?? '';
        $newStatus = $status['status'] ?? ''; // sent, delivered, read, failed
        $timestamp = $status['timestamp'] ?? '';

        if (empty($providerMessageId)) return;

        // Update whatsapp_messages status
        $statusMap = [
            'sent' => 'sent',
            'delivered' => 'delivered',
            'read' => 'read',
            'failed' => 'failed',
        ];

        $mappedStatus = $statusMap[$newStatus] ?? $newStatus;

        \DB::table('whatsapp_messages')
            ->where('tenant_id', $this->storeId)
            ->where('provider_message_id', $providerMessageId)
            ->update(['status' => $mappedStatus]);

        // Also update whatsapp_logs
        \DB::table('whatsapp_logs')
            ->where('tenant_id', $this->storeId)
            ->where('provider_message_id', $providerMessageId)
            ->update(['status' => $mappedStatus]);

        Log::info('WhatsApp status updated', [
            'message_id' => $providerMessageId,
            'status' => $mappedStatus,
            'store_id' => $this->storeId,
        ]);
    }

    /**
     * Log inbound action to whatsapp_logs
     */
    protected function logInboundAction(string $fromPhone, string $message, int $orderId = 0): void
    {
        try {
            $service = new WhatsAppService($this->storeId);
            \DB::table('whatsapp_logs')->insert([
                'order_id' => $orderId,
                'tenant_id' => $this->storeId,
                'direction' => 'inbound',
                'message_type' => 'customer_action',
                'message_body' => $message,
                'to_phone' => $service->getPhoneNumberId(),
                'from_phone' => $service->formatPhone($fromPhone),
                'status' => 'received',
                'provider_message_id' => null,
                'provider_response' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp inbound log failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessWhatsAppWebhook permanently failed', [
            'store_id' => $this->storeId,
            'error' => $exception->getMessage(),
        ]);
    }
}
