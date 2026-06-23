<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * ============================================================
     * UNIVERSAL WEBHOOK — Single URL for ALL stores
     * ============================================================
     *
     * Meta Dashboard setup (one-time, works for ALL stores):
     *   Callback URL: https://your-domain.com/webhook/whatsapp/universal
     *   Verify Token: <any store's verify token>
     *   Subscribed fields: messages
     *
     * How it routes:
     *   Meta sends phone_number_id in payload
     *   → We look up which store owns that phone_number_id
     *   → Route message to that store's tenant database
     */

    /**
     * Find store by phone_number_id from central mapping table
     */
    protected function findStoreByPhoneNumberId(string $phoneNumberId): ?object
    {
        $mapping = \DB::connection(config('tenancy.database.central_connection'))
            ->table('whatsapp_phone_mappings')
            ->where('phone_number_id', $phoneNumberId)
            ->where('is_active', true)
            ->first();

        if ($mapping) {
            $tenant = \App\Models\Tenant::find($mapping->tenant_id);
            if ($tenant) {
                return (object) [
                    'tenant_id' => $tenant->id,
                    'store_name' => $tenant->name,
                    'phone_number_id' => $phoneNumberId,
                ];
            }
        }

        return null;
    }

    /**
     * UNIVERSAL webhook verification — works for ALL stores
     * GET /webhook/whatsapp/universal
     */
    public function verifyUniversal(Request $request): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        Log::info('WhatsApp UNIVERSAL VERIFY', [
            'mode' => $mode,
            'token' => $token ? substr($token, 0, 10) . '...' : null,
        ]);

        if ($mode !== 'subscribe') {
            return response('Forbidden', 403);
        }

        // Check central whatsapp_providers table (super admin config)
        $provider = \DB::connection(config('tenancy.database.central_connection'))
            ->table('whatsapp_providers')
            ->where('is_active', true)
            ->first();

        if ($provider && $token === $provider->verify_token) {
            Log::info('Universal VERIFY: SUCCESS (central provider)');
            return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
        }

        // Check phone mappings table
        $mapping = \DB::connection(config('tenancy.database.central_connection'))
            ->table('whatsapp_phone_mappings')
            ->where('verify_token', $token)
            ->where('is_active', true)
            ->first();

        if ($mapping) {
            Log::info('Universal VERIFY: SUCCESS (phone mapping)', ['tenant_id' => $mapping->tenant_id]);
            return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('Universal VERIFY: FAILED', ['token' => $token]);
        return response('Forbidden', 403);
    }

    /**
     * UNIVERSAL webhook handler — Single endpoint for ALL stores
     * POST /webhook/whatsapp/universal
     */
    public function handleUniversal(Request $request): Response
    {
        $rawBody = $request->getContent();
        $payload = json_decode($rawBody, true);

        Log::info('WhatsApp UNIVERSAL WEBHOOK', [
            'payload_size' => strlen($rawBody),
        ]);

        if (!$payload) {
            Log::error('Universal: Invalid JSON');
            return response('OK', 200);
        }

        $phoneNumberId = $payload['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'] ?? null;

        if (!$phoneNumberId) {
            Log::warning('Universal: No phone_number_id');
            return response('OK', 200);
        }

        Log::info('Universal: phone_number_id received', ['phone_number_id' => $phoneNumberId]);

        $store = $this->findStoreByPhoneNumberId($phoneNumberId);

        if (!$store) {
            Log::warning('Universal: No store found', ['phone_number_id' => $phoneNumberId]);
            return response('OK', 200);
        }

        Log::info('Universal: Store found', [
            'tenant_id' => $store->tenant_id,
            'store_name' => $store->store_name,
        ]);

        $this->processWebhook($store->tenant_id, $payload);

        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Process webhook for a specific store
     */
    protected function processWebhook(string $tenantId, array $payload): void
    {
        if (!tenancy()->initialized) {
            $tenant = \App\Models\Tenant::find($tenantId);
            if (!$tenant) {
                Log::error('processWebhook: Tenant not found', ['tenant_id' => $tenantId]);
                return;
            }
            tenancy()->initialize($tenant);
        }

        $service = new WhatsAppService($tenantId);
        $messages = $payload['entry'][0]['changes'][0]['value']['messages'] ?? [];
        $statuses = $payload['entry'][0]['changes'][0]['value']['statuses'] ?? [];

        foreach ($messages as $msg) {
            $from = $msg['from'] ?? '';
            $type = $msg['type'] ?? '';
            $messageId = $msg['id'] ?? '';

            Log::info('processWebhook: Message', [
                'tenant_id' => $tenantId,
                'from' => $from,
                'type' => $type,
            ]);

            if ($type === 'text') {
                $this->processIncomingText($tenantId, $from, $msg['text']['body'] ?? '', $messageId, $service);
            } elseif ($type === 'interactive') {
                $this->processIncomingInteractive($tenantId, $from, $msg, $service);
            } elseif (in_array($type, ['image', 'document', 'audio', 'video'])) {
                $this->processIncomingMedia($tenantId, $from, $msg, $type, $service);
            }
        }

        foreach ($statuses as $status) {
            $this->processStatusUpdate($tenantId, $status);
        }
    }

    /**
     * Process incoming text message
     */
    protected function processIncomingText(string $storeId, string $from, string $text, string $messageId, WhatsAppService $service): void
    {
        $clean = preg_replace('/[^0-9]/', '', $from);
        $variants = array_unique([
            $clean,
            '+' . $clean,
            '0' . substr($clean, 2),
            substr($clean, 2),
        ]);

        Log::info('processIncomingText', [
            'store_id' => $storeId,
            'from' => $from,
            'variants' => $variants,
        ]);

        $conversation = null;
        foreach ($variants as $variant) {
            $conversation = \DB::table('whatsapp_conversations')
                ->where('tenant_id', $storeId)
                ->where('customer_phone', $variant)
                ->orderByDesc('last_message_at')
                ->first();
            if ($conversation) break;
        }

        if (!$conversation) {
            $last8 = substr($clean, -8);
            $conversation = \DB::table('whatsapp_conversations')
                ->where('tenant_id', $storeId)
                ->where('customer_phone', 'LIKE', '%' . $last8)
                ->orderByDesc('last_message_at')
                ->first();
        }

        if (!$conversation) {
            $formattedPhone = $service->formatPhone($from);

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

            Log::info('processIncomingText: NEW conversation', ['store_id' => $storeId, 'conversation_id' => $convId]);
        }

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

            \DB::table('whatsapp_conversations')
                ->where('id', $conversation->id)
                ->update([
                    'last_message_at' => now(),
                    'unread_count' => \DB::raw('unread_count + 1'),
                ]);

            Log::info('processIncomingText: MESSAGE SAVED', [
                'store_id' => $storeId,
                'conversation_id' => $conversation->id,
            ]);
        } catch (\Exception $e) {
            Log::error('processIncomingText: FAILED', ['store_id' => $storeId, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Process incoming media message
     */
    protected function processIncomingMedia(string $storeId, string $from, array $msg, string $type, WhatsAppService $service): void
    {
        $clean = preg_replace('/[^0-9]/', '', $from);
        $variants = array_unique([$clean, '+' . $clean, '0' . substr($clean, 2), substr($clean, 2)]);

        $conversation = null;
        foreach ($variants as $variant) {
            $conversation = \DB::table('whatsapp_conversations')
                ->where('tenant_id', $storeId)
                ->where('customer_phone', $variant)
                ->orderByDesc('last_message_at')
                ->first();
            if ($conversation) break;
        }

        if (!$conversation) {
            $last8 = substr($clean, -8);
            $conversation = \DB::table('whatsapp_conversations')
                ->where('tenant_id', $storeId)
                ->where('customer_phone', 'LIKE', '%' . $last8)
                ->first();
        }

        if ($conversation) {
            \DB::table('whatsapp_messages')->insert([
                'conversation_id' => $conversation->id,
                'tenant_id' => $storeId,
                'direction' => 'inbound',
                'message_type' => $type,
                'message_body' => "[{$type}] Incoming media",
                'from_phone' => $from,
                'to_phone' => $service->getPhoneNumberId(),
                'status' => 'received',
                'provider_message_id' => $msg['id'] ?? null,
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
     * Process incoming interactive button click
     */
    protected function processIncomingInteractive(string $storeId, string $from, array $msg, WhatsAppService $service): void
    {
        $buttonId = $msg['interactive']['button']['id'] ?? '';

        Log::info('processIncomingInteractive', ['store_id' => $storeId, 'button_id' => $buttonId]);

        if (preg_match('/^(confirm|cancel)_(\d+)$/', $buttonId, $matches)) {
            $action = $matches[1];
            $orderId = (int) $matches[2];

            $order = \App\Models\Order::find($orderId);
            if ($order) {
                $newStatus = $action === 'confirm' ? 'processing' : 'cancelled';
                $order->update(['status' => $newStatus]);

                $followUp = $action === 'confirm' ? 'order_confirmed' : 'order_cancelled';
                \App\Jobs\SendWhatsAppMessage::dispatch($storeId, $followUp, $order->customer_phone, null, $orderId);
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
    }

    /**
     * ============================================================
     * LEGACY PER-STORE ENDPOINTS (still supported)
     * ============================================================
     */

    public function verify(Request $request, string $tenantId): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode !== 'subscribe') return response('Forbidden', 403);

        $service = new WhatsAppService($tenantId);
        $validToken = $service->getVerifyToken();

        if (!empty($validToken) && $token === $validToken) {
            return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    public function handle(Request $request, string $tenantId): Response
    {
        $payload = json_decode($request->getContent(), true);

        Log::info('WhatsApp WEBHOOK (per-store)', ['store_id' => $tenantId]);

        if (!$payload || !isset($payload['entry'][0]['changes'][0])) {
            return response('OK', 200);
        }

        $this->processWebhook($tenantId, $payload);

        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    public function incoming(Request $request, string $tenantId): Response
    {
        return $this->handle($request, $tenantId);
    }

    public function test(string $tenantId): Response
    {
        return response()->json([
            'status' => 'ok',
            'store_id' => $tenantId,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function simulate(Request $request, string $tenantId): Response
    {
        $phone = $request->input('phone', '923288847190');
        $text = $request->input('text', 'Test message');

        $testPayload = [
            'entry' => [[
                'changes' => [[
                    'value' => [
                        'metadata' => ['phone_number_id' => '1172546945946113'],
                        'messages' => [[
                            'from' => $phone,
                            'id' => 'wamid.test.' . time(),
                            'timestamp' => (string) time(),
                            'type' => 'text',
                            'text' => ['body' => $text],
                        ]],
                    ],
                ]],
            ]],
        ];

        $this->processWebhook($tenantId, $testPayload);

        return response()->json(['status' => 'simulated', 'store_id' => $tenantId]);
    }
}
