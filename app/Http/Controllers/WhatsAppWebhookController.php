<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle webhook verification from Meta.
     *
     * GET /webhook/whatsapp/{tenantId}
     */
    public function verify(Request $request, string $tenantId): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        Log::info('WhatsApp VERIFY attempt', [
            'store_id' => $tenantId,
            'hub_mode' => $mode,
            'token_received' => $token ? substr($token, 0, 10) . '...' : null,
            'challenge' => $challenge,
        ]);

        if ($mode !== 'subscribe') {
            Log::warning('WhatsApp VERIFY failed: mode != subscribe', ['mode' => $mode]);
            return response('Forbidden', 403);
        }

        $service = new WhatsAppService($tenantId);
        $validToken = $service->getVerifyToken();

        Log::info('WhatsApp VERIFY token check', [
            'store_id' => $tenantId,
            'valid_token_exists' => !empty($validToken),
            'match' => $token === $validToken,
        ]);

        if (!empty($validToken) && $token === $validToken) {
            Log::info('WhatsApp VERIFY SUCCESS', ['store_id' => $tenantId]);
            return response((string) $challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        Log::warning('WhatsApp VERIFY FAILED: token mismatch', [
            'store_id' => $tenantId,
        ]);

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming webhook events from Meta.
     */
    public function handle(Request $request, string $tenantId): Response
    {
        $rawBody = $request->getContent();
        $payload = json_decode($rawBody, true);

        // CRITICAL: Log the FULL raw body to see exactly what Meta sends
        Log::info('WhatsApp WEBHOOK POST RAW BODY', [
            'store_id' => $tenantId,
            'raw_body' => $rawBody,
        ]);

        if (!$payload) {
            Log::error('WhatsApp WEBHOOK: Failed to decode JSON payload', [
                'store_id' => $tenantId,
                'raw_body' => substr($rawBody, 0, 500),
            ]);
            return response('OK', 200);
        }

        // Extract metadata
        $metadata = $payload['entry'][0]['changes'][0]['value']['metadata'] ?? null;
        $messages = $payload['entry'][0]['changes'][0]['value']['messages'] ?? [];
        $statuses = $payload['entry'][0]['changes'][0]['value']['statuses'] ?? [];

        Log::info('WhatsApp WEBHOOK PARSED', [
            'store_id' => $tenantId,
            'phone_number_id' => $metadata['phone_number_id'] ?? 'N/A',
            'message_count' => count($messages),
            'status_count' => count($statuses),
            'messages' => $messages,
        ]);

        if (empty($messages) && empty($statuses)) {
            Log::info('WhatsApp WEBHOOK: nothing to process', ['store_id' => $tenantId]);
            return response('OK', 200);
        }

        // Process SYNCHRONOUSLY - no queue, no job, direct processing
        try {
            $service = new WhatsAppService($tenantId);

            // Initialize tenancy
            if (!tenancy()->initialized) {
                $tenant = \App\Models\Tenant::find($tenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                    Log::info('WhatsApp WEBHOOK: Tenancy initialized', ['store_id' => $tenantId]);
                } else {
                    Log::error('WhatsApp WEBHOOK: Tenant NOT FOUND', ['store_id' => $tenantId]);
                    return response('OK', 200);
                }
            }

            // Process each message directly
            foreach ($messages as $msg) {
                $from = $msg['from'] ?? '';
                $type = $msg['type'] ?? '';
                $messageId = $msg['id'] ?? '';

                Log::info('WhatsApp WEBHOOK: Processing message', [
                    'store_id' => $tenantId,
                    'from' => $from,
                    'type' => $type,
                    'message_id' => $messageId,
                ]);

                if ($type === 'text') {
                    $text = $msg['text']['body'] ?? '';
                    $this->processIncomingText($tenantId, $from, $text, $messageId, $service);
                } elseif ($type === 'interactive') {
                    $this->processIncomingInteractive($tenantId, $from, $msg, $service);
                }
            }

            // Process status updates
            foreach ($statuses as $status) {
                $this->processStatusUpdate($tenantId, $status);
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp WEBHOOK: Processing FAILED', [
                'store_id' => $tenantId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response('OK', 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Process incoming text message DIRECTLY (no job queue)
     */
    protected function processIncomingText(string $storeId, string $from, string $text, string $messageId, WhatsAppService $service): void
    {
        Log::info('processIncomingText: START', [
            'store_id' => $storeId,
            'from' => $from,
            'text' => substr($text, 0, 50),
        ]);

        // Generate phone variants
        $clean = preg_replace('/[^0-9]/', '', $from);
        $variants = array_unique([
            $clean,                          // 923288847190
            '+' . $clean,                    // +923288847190
            '0' . substr($clean, 2),         // 03288847190
            '+' . $clean,                    // +923288847190
            substr($clean, 2),               // 3288847190
        ]);

        Log::info('processIncomingText: Phone variants', [
            'store_id' => $storeId,
            'variants' => $variants,
        ]);

        // Search for existing conversation
        $conversation = null;
        foreach ($variants as $variant) {
            $conversation = \DB::table('whatsapp_conversations')
                ->where('tenant_id', $storeId)
                ->where('customer_phone', $variant)
                ->orderByDesc('last_message_at')
                ->first();

            if ($conversation) {
                Log::info('processIncomingText: FOUND conversation', [
                    'store_id' => $storeId,
                    'conversation_id' => $conversation->id,
                    'matched_variant' => $variant,
                    'existing_phone' => $conversation->customer_phone,
                ]);
                break;
            }
        }

        // If no conversation found, also try LIKE search on last 8 digits
        if (!$conversation) {
            $last8 = substr($clean, -8);
            $conversation = \DB::table('whatsapp_conversations')
                ->where('tenant_id', $storeId)
                ->where('customer_phone', 'LIKE', '%' . $last8)
                ->orderByDesc('last_message_at')
                ->first();

            if ($conversation) {
                Log::info('processIncomingText: FOUND conversation via LIKE', [
                    'store_id' => $storeId,
                    'conversation_id' => $conversation->id,
                    'existing_phone' => $conversation->customer_phone,
                ]);
            }
        }

        // Auto-create if still not found
        if (!$conversation) {
            Log::info('processIncomingText: No conversation found, AUTO-CREATING', [
                'store_id' => $storeId,
                'from' => $from,
            ]);

            $formattedPhone = $service->formatPhone($from);

            // Check customers table
            $customer = \DB::table('customers')
                ->where('tenant_id', $storeId)
                ->where(function ($q) use ($formattedPhone, $clean) {
                    $q->where('phone', $formattedPhone)
                      ->orWhere('phone', $clean)
                      ->orWhere('phone', 'LIKE', '%' . substr($formattedPhone, -8) . '%');
                })
                ->first();

            $convId = \DB::table('whatsapp_conversations')->insertGetId([
                'tenant_id' => $storeId,
                'order_id' => null,
                'customer_id' => $customer->id ?? null,
                'customer_name' => $customer->name ?? ('Customer ' . substr($from, -4)),
                'customer_phone' => $formattedPhone,
                'status' => 'open',
                'last_message_at' => now(),
                'unread_count' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $conversation = (object) ['id' => $convId, 'order_id' => null];

            Log::info('processIncomingText: NEW conversation created', [
                'store_id' => $storeId,
                'conversation_id' => $convId,
                'phone' => $formattedPhone,
            ]);
        }

        // Save the inbound message
        try {
            \DB::table('whatsapp_messages')->insert([
                'conversation_id' => $conversation->id,
                'tenant_id' => $storeId,
                'direction' => 'inbound',
                'message_type' => 'text',
                'message_body' => $text,
                'from_phone' => $from,
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

            Log::info('processIncomingText: MESSAGE SAVED', [
                'store_id' => $storeId,
                'conversation_id' => $conversation->id,
                'message' => substr($text, 0, 50),
            ]);
        } catch (\Exception $e) {
            Log::error('processIncomingText: DB INSERT FAILED', [
                'store_id' => $storeId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process incoming interactive button click
     */
    protected function processIncomingInteractive(string $storeId, string $from, array $msg, WhatsAppService $service): void
    {
        $buttonId = $msg['interactive']['button']['id'] ?? '';
        $buttonTitle = $msg['interactive']['button']['title'] ?? '';

        Log::info('processIncomingInteractive', [
            'store_id' => $storeId,
            'from' => $from,
            'button_id' => $buttonId,
        ]);

        if (preg_match('/^(confirm|cancel)_(\d+)$/', $buttonId, $matches)) {
            $action = $matches[1];
            $orderId = (int) $matches[2];

            $order = \App\Models\Order::find($orderId);
            if ($order) {
                $newStatus = $action === 'confirm' ? 'processing' : 'cancelled';
                $order->update(['status' => $newStatus]);

                Log::info('processIncomingInteractive: Order updated', [
                    'order_id' => $orderId,
                    'new_status' => $newStatus,
                ]);

                // Send follow-up
                $followUp = $action === 'confirm' ? 'order_confirmed' : 'order_cancelled';
                \App\Jobs\SendWhatsAppMessage::dispatch(
                    $storeId,
                    $followUp,
                    $order->customer_phone,
                    null,
                    $orderId
                );
            }
        }
    }

    /**
     * Process status update
     */
    protected function processStatusUpdate(string $storeId, array $status): void
    {
        $providerMessageId = $status['id'] ?? '';
        $newStatus = $status['status'] ?? '';

        if (empty($providerMessageId)) return;

        $statusMap = ['sent' => 'sent', 'delivered' => 'delivered', 'read' => 'read', 'failed' => 'failed'];
        $mapped = $statusMap[$newStatus] ?? $newStatus;

        \DB::table('whatsapp_messages')
            ->where('tenant_id', $storeId)
            ->where('provider_message_id', $providerMessageId)
            ->update(['status' => $mapped]);

        \DB::table('whatsapp_logs')
            ->where('tenant_id', $storeId)
            ->where('provider_message_id', $providerMessageId)
            ->update(['status' => $mapped]);

        Log::info('processStatusUpdate', [
            'store_id' => $storeId,
            'message_id' => $providerMessageId,
            'status' => $mapped,
        ]);
    }

    /**
     * Handle incoming messages via the alternative incoming webhook endpoint.
     *
     * POST /webhook/whatsapp/{tenantId}/incoming
     */
    public function incoming(Request $request, string $tenantId): Response
    {
        $rawBody = $request->getContent();
        $payload = json_decode($rawBody, true);

        Log::info('WhatsApp INCOMING webhook', [
            'store_id' => $tenantId,
            'raw_body' => substr($rawBody, 0, 2000),
        ]);

        if (!$payload || !isset($payload['entry'][0]['changes'][0])) {
            Log::warning('WhatsApp INCOMING: invalid payload', ['store_id' => $tenantId]);
            return response('OK', 200);
        }

        // Reuse the same handle method
        return $this->handle($request, $tenantId);
    }
}
