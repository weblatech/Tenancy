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

    protected string $storeId;
    protected array $payload;

    public function __construct(string $storeId, array $payload)
    {
        $this->storeId = $storeId;
        $this->payload = $payload;
    }

    /**
     * Execute the webhook processing job.
     */
    public function handle(): void
    {
        Log::info('ProcessWhatsAppWebhook: STARTED', ['store_id' => $this->storeId]);

        // ── Initialize tenancy ──
        if (!tenancy()->initialized) {
            $tenant = \App\Models\Tenant::find($this->storeId);
            if (!$tenant) {
                Log::error('ProcessWhatsAppWebhook: Tenant NOT FOUND', ['store_id' => $this->storeId]);
                return;
            }
            tenancy()->initialize($tenant);
            Log::info('ProcessWhatsAppWebhook: Tenancy initialized', ['store_id' => $this->storeId]);
        }

        $service = new WhatsAppService($this->storeId);

        // ── Extract entry → changes → value ──
        $entry = $this->payload['entry'][0] ?? null;
        if (!$entry) {
            Log::warning('ProcessWhatsAppWebhook: No entry in payload', ['store_id' => $this->storeId]);
            return;
        }

        $changes = $entry['changes'][0] ?? null;
        if (!$changes) {
            Log::warning('ProcessWhatsAppWebhook: No changes in entry', ['store_id' => $this->storeId]);
            return;
        }

        $value = $changes['value'] ?? [];
        $metadata = $value['metadata'] ?? [];

        Log::info('ProcessWhatsAppWebhook: Payload structure parsed', [
            'store_id' => $this->storeId,
            'phone_number_id' => $metadata['phone_number_id'] ?? 'N/A',
            'has_messages' => isset($value['messages']),
            'has_statuses' => isset($value['statuses']),
        ]);

        // ── Process MESSAGES ──
        $messages = $value['messages'] ?? [];
        foreach ($messages as $index => $message) {
            Log::info("ProcessWhatsAppWebhook: Processing message #{$index}", [
                'store_id' => $this->storeId,
                'message_type' => $message['type'] ?? 'unknown',
                'from' => $message['from'] ?? 'N/A',
                'message_id' => $message['id'] ?? 'N/A',
            ]);

            $from = $message['from'] ?? '';
            $messageId = $message['id'] ?? '';
            $type = $message['type'] ?? '';

            match ($type) {
                'interactive' => $this->handleButtonClick($message, $from, $service),
                'text' => $this->handleTextMessage($from, $message, $messageId, $service),
                'image' => $this->handleMediaMessage($from, $message, 'image', $service),
                'document' => $this->handleMediaMessage($from, $message, 'document', $service),
                'audio' => $this->handleMediaMessage($from, $message, 'audio', $service),
                'video' => $this->handleMediaMessage($from, $message, 'video', $service),
                default => Log::info('ProcessWhatsAppWebhook: Unhandled message type', [
                    'store_id' => $this->storeId,
                    'type' => $type,
                ]),
            };
        }

        // ── Process STATUSES ──
        $statuses = $value['statuses'] ?? [];
        foreach ($statuses as $status) {
            $this->handleStatusUpdate($status);
        }

        Log::info('ProcessWhatsAppWebhook: COMPLETED', [
            'store_id' => $this->storeId,
            'messages_processed' => count($messages),
            'statuses_processed' => count($statuses),
        ]);
    }

    /**
     * Handle interactive button click (Confirm / Cancel order)
     */
    protected function handleButtonClick(array $message, string $from, WhatsAppService $service): void
    {
        $buttonId = $message['interactive']['button']['id'] ?? '';
        $buttonTitle = $message['interactive']['button']['title'] ?? '';

        Log::info('ProcessWhatsAppWebhook: Button click', [
            'store_id' => $this->storeId,
            'from' => $from,
            'button_id' => $buttonId,
            'button_title' => $buttonTitle,
        ]);

        if (!preg_match('/^(confirm|cancel)_(\d+)$/', $buttonId, $matches)) {
            Log::info('ProcessWhatsAppWebhook: Unknown button ID format', ['button_id' => $buttonId]);
            return;
        }

        $action = $matches[1];
        $orderId = (int) $matches[2];

        $order = Order::find($orderId);
        if (!$order) {
            Log::warning('ProcessWhatsAppWebhook: Order not found for button click', ['order_id' => $orderId]);
            return;
        }

        $newStatus = $action === 'confirm' ? 'processing' : 'cancelled';
        $order->update(['status' => $newStatus]);

        Log::info('ProcessWhatsAppWebhook: Order status updated via button', [
            'order_id' => $orderId,
            'new_status' => $newStatus,
            'store_id' => $this->storeId,
        ]);

        $this->logInboundAction($from, "Customer clicked: {$buttonTitle}", $orderId);

        // Send follow-up
        $followUpType = $action === 'confirm' ? 'order_confirmed' : 'order_cancelled';
        SendWhatsAppMessage::dispatch(
            $this->storeId,
            $followUpType,
            $order->customer_phone,
            null,
            $orderId
        );
    }

    /**
     * Handle incoming text message
     *
     * CRITICAL: This must work even if no prior conversation exists.
     * Meta sends phone like "923288847190" (no + prefix).
     * We try multiple phone formats to match against the DB.
     */
    protected function handleTextMessage(string $from, array $message, string $messageId, WhatsAppService $service): void
    {
        $rawText = $message['text']['body'] ?? '';

        Log::info('ProcessWhatsAppWebhook: Text message received', [
            'store_id' => $this->storeId,
            'from_raw' => $from,
            'text' => substr($rawText, 0, 100),
            'message_id' => $messageId,
        ]);

        // ── Generate ALL possible phone format variations ──
        $phoneVariants = $this->getPhoneVariants($from);

        Log::info('ProcessWhatsAppWebhook: Phone variants generated', [
            'store_id' => $this->storeId,
            'from' => $from,
            'variants' => $phoneVariants,
        ]);

        // ── Try to find conversation with ANY phone variant ──
        $conversation = null;
        foreach ($phoneVariants as $variant) {
            $conversation = \DB::table('whatsapp_conversations')
                ->where('tenant_id', $this->storeId)
                ->where('customer_phone', $variant)
                ->orderByDesc('last_message_at')
                ->first();

            if ($conversation) {
                Log::info('ProcessWhatsAppWebhook: Found conversation with phone variant', [
                    'store_id' => $this->storeId,
                    'matched_variant' => $variant,
                    'conversation_id' => $conversation->id,
                ]);
                break;
            }
        }

        // ── If no conversation found, try to find customer by phone and create conversation ──
        if (!$conversation) {
            Log::info('ProcessWhatsAppWebhook: No conversation found, attempting auto-create', [
                'store_id' => $this->storeId,
                'from' => $from,
            ]);

            $conversation = $this->autoCreateConversation($from, $service);
        }

        if (!$conversation) {
            Log::warning('ProcessWhatsAppWebhook: Could not create conversation', [
                'store_id' => $this->storeId,
                'from' => $from,
                'phone_variants' => $phoneVariants,
            ]);
            return;
        }

        // ── Store the inbound message ──
        try {
            $insertData = [
                'conversation_id' => $conversation->id,
                'tenant_id' => $this->storeId,
                'direction' => 'inbound',
                'message_type' => 'text',
                'message_body' => $rawText,
                'from_phone' => $from,
                'to_phone' => $service->getPhoneNumberId(),
                'status' => 'received',
                'provider_message_id' => $messageId,
                'is_auto' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Log::info('ProcessWhatsAppWebhook: Inserting message', [
                'store_id' => $this->storeId,
                'conversation_id' => $conversation->id,
            ]);

            \DB::table('whatsapp_messages')->insert($insertData);

            // Update conversation
            \DB::table('whatsapp_conversations')
                ->where('id', $conversation->id)
                ->update([
                    'last_message_at' => now(),
                    'unread_count' => \DB::raw('unread_count + 1'),
                ]);

            // Log to whatsapp_logs
            $this->logInboundAction($from, $rawText, $conversation->order_id ?? 0);

            Log::info('ProcessWhatsAppWebhook: Message SAVED successfully', [
                'store_id' => $this->storeId,
                'conversation_id' => $conversation->id,
                'message_body' => substr($rawText, 0, 50),
            ]);
        } catch (\Exception $e) {
            Log::error('ProcessWhatsAppWebhook: FAILED to save message', [
                'store_id' => $this->storeId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }

    /**
     * Generate all possible phone format variants for matching.
     *
     * Meta sends: "923288847190" (raw, no +, no leading 0)
     * DB might store: "+923288847190", "923288847190", "03288847190", "3288847190"
     */
    protected function getPhoneVariants(string $phone): array
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        $variants = [$clean]; // raw as Meta sends it

        // Add with + prefix
        $variants[] = '+' . $clean;

        // If starts with 92 (Pakistan), add local format
        if (str_starts_with($clean, '92') && strlen($clean) >= 12) {
            $local = '0' . substr($clean, 2);
            $variants[] = $local;
            $variants[] = '+' . $clean;
            // Also try without country code
            $variants[] = substr($clean, 2);
        }

        // If starts with 0, add international format
        if (str_starts_with($clean, '0') && strlen($clean) >= 11) {
            $international = '92' . substr($clean, 1);
            $variants[] = $international;
            $variants[] = '+' . $international;
            // Also try without leading 0
            $variants[] = substr($clean, 1);
        }

        return array_unique($variants);
    }

    /**
     * Auto-create a conversation when none exists for the phone number.
     *
     * This ensures messages from new customers are never silently dropped.
     */
    protected function autoCreateConversation(string $from, WhatsAppService $service): ?object
    {
        $formattedPhone = $service->formatPhone($from);

        Log::info('ProcessWhatsAppWebhook: autoCreateConversation', [
            'store_id' => $this->storeId,
            'from' => $from,
            'formatted' => $formattedPhone,
        ]);

        // Check if customer exists in customers table
        $customer = \DB::table('customers')
            ->where('tenant_id', $this->storeId)
            ->where(function ($query) use ($formattedPhone, $from) {
                $query->where('phone', $formattedPhone)
                    ->orWhere('phone', $from)
                    ->orWhere('phone', 'like', '%' . substr($formattedPhone, -8) . '%');
            })
            ->first();

        $customerName = $customer->name ?? 'Customer ' . substr($from, -4);
        $customerId = $customer->id ?? null;

        try {
            $convId = \DB::table('whatsapp_conversations')->insertGetId([
                'tenant_id' => $this->storeId,
                'order_id' => null,
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'customer_phone' => $formattedPhone,
                'status' => 'open',
                'last_message_at' => now(),
                'unread_count' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('ProcessWhatsAppWebhook: New conversation CREATED', [
                'store_id' => $this->storeId,
                'conversation_id' => $convId,
                'customer_phone' => $formattedPhone,
            ]);

            return (object) [
                'id' => $convId,
                'order_id' => null,
            ];
        } catch (\Exception $e) {
            Log::error('ProcessWhatsAppWebhook: autoCreateConversation FAILED', [
                'store_id' => $this->storeId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Handle incoming media message
     */
    protected function handleMediaMessage(string $from, array $message, string $mediaType, WhatsAppService $service): void
    {
        Log::info('ProcessWhatsAppWebhook: Media message', [
            'store_id' => $this->storeId,
            'from' => $from,
            'media_type' => $mediaType,
        ]);

        $phoneVariants = $this->getPhoneVariants($from);
        $conversation = null;

        foreach ($phoneVariants as $variant) {
            $conversation = \DB::table('whatsapp_conversations')
                ->where('tenant_id', $this->storeId)
                ->where('customer_phone', $variant)
                ->orderByDesc('last_message_at')
                ->first();
            if ($conversation) break;
        }

        if (!$conversation) {
            $conversation = $this->autoCreateConversation($from, $service);
        }

        if (!$conversation) {
            Log::warning('ProcessWhatsAppWebhook: No conversation for media', ['from' => $from]);
            return;
        }

        try {
            \DB::table('whatsapp_messages')->insert([
                'conversation_id' => $conversation->id,
                'tenant_id' => $this->storeId,
                'direction' => 'inbound',
                'message_type' => $mediaType,
                'message_body' => "[{$mediaType}] Incoming {$mediaType}",
                'from_phone' => $from,
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

            Log::info('ProcessWhatsAppWebhook: Media message SAVED', [
                'store_id' => $this->storeId,
                'conversation_id' => $conversation->id,
                'media_type' => $mediaType,
            ]);
        } catch (\Exception $e) {
            Log::error('ProcessWhatsAppWebhook: Media message FAILED', [
                'store_id' => $this->storeId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle message status updates
     */
    protected function handleStatusUpdate(array $status): void
    {
        $providerMessageId = $status['id'] ?? '';
        $newStatus = $status['status'] ?? '';

        if (empty($providerMessageId)) return;

        $statusMap = [
            'sent' => 'sent',
            'delivered' => 'delivered',
            'read' => 'read',
            'failed' => 'failed',
        ];
        $mappedStatus = $statusMap[$newStatus] ?? $newStatus;

        $updated1 = \DB::table('whatsapp_messages')
            ->where('tenant_id', $this->storeId)
            ->where('provider_message_id', $providerMessageId)
            ->update(['status' => $mappedStatus]);

        $updated2 = \DB::table('whatsapp_logs')
            ->where('tenant_id', $this->storeId)
            ->where('provider_message_id', $providerMessageId)
            ->update(['status' => $mappedStatus]);

        Log::info('ProcessWhatsAppWebhook: Status update', [
            'store_id' => $this->storeId,
            'message_id' => $providerMessageId,
            'new_status' => $mappedStatus,
            'messages_updated' => $updated1,
            'logs_updated' => $updated2,
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
                'order_id' => $orderId ?: null,
                'tenant_id' => $this->storeId,
                'direction' => 'inbound',
                'message_type' => 'customer_action',
                'message_body' => $message,
                'to_phone' => $service->getPhoneNumberId(),
                'from_phone' => $fromPhone,
                'status' => 'received',
                'provider_message_id' => null,
                'provider_response' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('ProcessWhatsAppWebhook: logInboundAction FAILED', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessWhatsAppWebhook: JOB FAILED permanently', [
            'store_id' => $this->storeId,
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }
}
