<?php

namespace App\Services;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppCRM
{
    protected $settings;
    protected $apiKey;
    protected $phoneNumberId;
    protected $verifyToken;
    protected $baseUrl = 'https://graph.facebook.com/v18.0';

    public function __construct()
    {
        $this->settings = StoreSetting::firstOrCreate(['id' => 1]);
        $this->apiKey = $this->settings->whatsapp_api_key ?? '';
        $this->phoneNumberId = $this->settings->whatsapp_phone_number_id ?? '';
        $this->verifyToken = $this->settings->whatsapp_verify_token ?? '';
    }

    /**
     * Get the shared API provider from central database
     */
    public static function getProvider(): ?object
    {
        try {
            return \DB::connection(config('tenancy.database.central_connection'))
                ->table('whatsapp_providers')
                ->where('is_active', true)
                ->first();
        } catch (\Exception $e) {
            Log::error('WhatsApp getProvider error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if WhatsApp CRM is configured for this tenant
     */
    public function isConfigured(): bool
    {
        $provider = self::getProvider();
        $hasCrmActive = !empty($this->settings->whatsapp_crm_active);
        $hasPhoneId = !empty($this->settings->whatsapp_phone_number_id);
        $hasApiKey = !empty($this->getApiKey());

        if (!$provider) {
            Log::warning('WhatsApp CRM: No active provider in central DB');
        }
        if (!$hasCrmActive) {
            Log::warning('WhatsApp CRM: whatsapp_crm_active is false for tenant ' . tenant('id'));
        }
        if (!$hasPhoneId) {
            Log::warning('WhatsApp CRM: whatsapp_phone_number_id is empty for tenant ' . tenant('id'));
        }

        return $provider && $hasCrmActive && $hasPhoneId && $hasApiKey;
    }

    /**
     * Get the API key from provider or tenant settings
     */
    public function getApiKey(): string
    {
        $provider = self::getProvider();
        return $provider->api_key ?? $this->apiKey;
    }

    /**
     * Get the phone number ID from tenant settings (preferred) or provider (fallback)
     */
    public function getPhoneNumberId(): string
    {
        if (!empty($this->phoneNumberId)) {
            return $this->phoneNumberId;
        }
        $provider = self::getProvider();
        return $provider->phone_number_id ?? '';
    }

    /**
     * Get store phone number for display
     */
    public function getStorePhone(): string
    {
        return $this->settings->footer_whatsapp ?? '';
    }

    /**
     * Format phone number for WhatsApp API (E.164 format)
     */
    public function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            $phone = '92' . substr($phone, 1);
        }
        if (strlen($phone) === 10 && str_starts_with($phone, '3')) {
            $phone = '92' . $phone;
        }
        if (strlen($phone) === 12 && str_starts_with($phone, '92')) {
            return $phone;
        }
        return $phone;
    }

    /**
     * Generate order summary text
     */
    public function getOrderSummary(Order $order): string
    {
        $items = $order->cart_items ?? [];
        $itemLines = '';
        foreach ($items as $item) {
            $name = $item['name'] ?? $item['product_name'] ?? 'Product';
            $qty = $item['quantity'] ?? 1;
            $price = $item['price'] ?? 0;
            $itemLines .= "- {$name} x{$qty} = Rs. " . number_format($price * $qty) . "\n";
        }
        return $itemLines;
    }

    /**
     * Build custom message from template
     */
    public function buildMessage(string $template, Order $order): string
    {
        $storeName = tenant('name') ?? 'Our Store';
        $items = $this->getOrderSummary($order);
        $total = number_format($order->total);
        $address = "{$order->customer_address}, {$order->customer_city}";

        $replacements = [
            '{customer_name}' => $order->customer_name,
            '{order_id}' => '#' . $order->id,
            '{store_name}' => $storeName,
            '{items}' => $items,
            '{total}' => $total,
            '{address}' => $address,
            '{phone}' => $order->customer_phone,
            '{payment_method}' => strtoupper($order->payment_method),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Send order pending confirmation to customer
     */
    public function sendOrderPending(Order $order): array
    {
        $storeName = tenant('name') ?? 'Our Store';
        $customerName = $order->customer_name;
        $phone = $this->formatPhone($order->customer_phone);
        $orderItems = $this->getOrderSummary($order);
        $total = number_format($order->total);
        $address = "{$order->customer_address}, {$order->customer_city}";
        $paymentMethod = strtoupper($order->payment_method);
        $orderId = '#' . $order->id;

        // Use custom template if set
        $customTemplate = $this->settings->whatsapp_msg_order_pending;
        if (!empty($customTemplate)) {
            $message = $this->buildMessage($customTemplate, $order);
        } else {
            $message = "Hello {$customerName}! 👋\n\n";
            $message .= "Thank you for your order at *{$storeName}*!\n\n";
            $message .= "📋 *Order {$orderId}*\n";
            $message .= "━━━━━━━━━━━━━━━━━\n";
            $message .= "{$orderItems}\n";
            $message .= "━━━━━━━━━━━━━━━━━\n";
            $message .= "💰 *Total: Rs. {$total}*\n";
            $message .= "💳 Payment: {$paymentMethod}\n";
            $message .= "📍 Address: {$address}\n";
            $message .= "📞 Phone: {$order->customer_phone}\n\n";
            $message .= "Please confirm your order:\n";
        }

        $buttons = [
            ['type' => 'reply', 'reply' => ['id' => "confirm_{$order->id}", 'title' => '✅ Confirm Order']],
            ['type' => 'reply', 'reply' => ['id' => "cancel_{$order->id}", 'title' => '❌ Cancel Order']],
        ];

        return $this->sendInteractiveMessage($phone, $message, $buttons, $order->id, 'order_pending');
    }

    /**
     * Send order confirmed notification
     */
    public function sendOrderConfirmed(Order $order): array
    {
        $phone = $this->formatPhone($order->customer_phone);
        $customTemplate = $this->settings->whatsapp_msg_order_confirmed;

        if (!empty($customTemplate)) {
            $message = $this->buildMessage($customTemplate, $order);
        } else {
            $storeName = tenant('name') ?? 'Our Store';
            $message = "Hello {$order->customer_name}! 🎉\n\n";
            $message .= "Your order *#" . $order->id . "* at *{$storeName}* has been *confirmed*!\n\n";
            $message .= "💰 Total: Rs. " . number_format($order->total) . "\n";
            $message .= "📦 Status: Processing\n\n";
            $message .= "We'll update you when your order is on the way. Thank you! 🙏";
        }

        return $this->sendTextMessage($phone, $message, $order->id, 'order_confirmed');
    }

    /**
     * Send order processing notification
     */
    public function sendOrderProcessing(Order $order): array
    {
        $phone = $this->formatPhone($order->customer_phone);
        $customTemplate = $this->settings->whatsapp_msg_order_processing;

        if (!empty($customTemplate)) {
            $message = $this->buildMessage($customTemplate, $order);
        } else {
            $storeName = tenant('name') ?? 'Our Store';
            $message = "Hello {$order->customer_name}! 🚚\n\n";
            $message .= "Your order *#" . $order->id . "* at *{$storeName}* is now *processing*.\n\n";
            $message .= "Your order is being prepared and will be shipped soon. 📦";
        }

        return $this->sendTextMessage($phone, $message, $order->id, 'order_processing');
    }

    /**
     * Send order completed notification
     */
    public function sendOrderCompleted(Order $order): array
    {
        $phone = $this->formatPhone($order->customer_phone);
        $customTemplate = $this->settings->whatsapp_msg_order_completed;

        if (!empty($customTemplate)) {
            $message = $this->buildMessage($customTemplate, $order);
        } else {
            $storeName = tenant('name') ?? 'Our Store';
            $message = "Hello {$order->customer_name}! ✅\n\n";
            $message .= "Your order *#" . $order->id . "* at *{$storeName}* has been *delivered*!\n\n";
            $message .= "💰 Total: Rs. " . number_format($order->total) . "\n\n";
            $message .= "Thank you for shopping with us! 🙏";
        }

        return $this->sendTextMessage($phone, $message, $order->id, 'order_completed');
    }

    /**
     * Send order cancelled notification
     */
    public function sendOrderCancelled(Order $order, string $reason = ''): array
    {
        $phone = $this->formatPhone($order->customer_phone);
        $customTemplate = $this->settings->whatsapp_msg_order_cancelled;

        if (!empty($customTemplate)) {
            $message = $this->buildMessage($customTemplate, $order);
        } else {
            $storeName = tenant('name') ?? 'Our Store';
            $message = "Hello {$order->customer_name},\n\n";
            $message .= "Your order *#" . $order->id . "* at *{$storeName}* has been *cancelled*.\n\n";
            if (!empty($reason)) {
                $message .= "Reason: {$reason}\n\n";
            }
            $message .= "If this was a mistake, please feel free to place a new order. 🙏";
        }

        return $this->sendTextMessage($phone, $message, $order->id, 'order_cancelled');
    }

    /**
     * Send a manual chat message from store owner to customer
     */
    public function sendChatMessage(string $phone, string $message, int $conversationId): array
    {
        $formattedPhone = $this->formatPhone($phone);
        $result = $this->sendTextMessage($formattedPhone, $message, 0, 'manual_chat');
        return $result;
    }

    /**
     * Send interactive message with buttons
     */
    protected function sendInteractiveMessage(string $phone, string $text, array $buttons, int $orderId, string $type): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp API not configured'];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => ['text' => $text],
                'action' => ['buttons' => $buttons],
            ],
        ];

        return $this->sendRequest($payload, $orderId, $type, $phone);
    }

    /**
     * Send simple text message
     */
    public function sendTextMessage(string $phone, string $text, int $orderId = 0, string $type = 'text'): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp API not configured'];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'text',
            'text' => ['body' => $text],
        ];

        return $this->sendRequest($payload, $orderId, $type, $phone);
    }

    /**
     * Send API request to WhatsApp
     */
    protected function sendRequest(array $payload, int $orderId, string $type, string $phone): array
    {
        try {
            $apiKey = $this->getApiKey();
            $phoneId = $this->getPhoneNumberId();

            if (empty($apiKey)) {
                return ['success' => false, 'error' => 'API Key (Access Token) is empty. Save it in Super Admin > WhatsApp Provider.'];
            }
            if (empty($phoneId)) {
                return ['success' => false, 'error' => 'Phone Number ID is empty. Save it in CRM Settings.'];
            }

            Log::info('WhatsApp API Request', [
                'url' => "{$this->baseUrl}/{$phoneId}/messages",
                'to' => $phone,
                'type' => $type,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post(
                "{$this->baseUrl}/{$phoneId}/messages",
                $payload
            );

            $body = $response->json();

            $result = [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'response' => $body,
            ];

            if (!$response->successful()) {
                $errorMsg = $body['error']['message'] ?? 'Unknown error';
                $errorCode = $body['error']['code'] ?? 0;
                Log::error('WhatsApp API Error', [
                    'status' => $response->status(),
                    'error_code' => $errorCode,
                    'error_message' => $errorMsg,
                    'phone' => $phone,
                    'phone_id' => $phoneId,
                ]);
                $result['error'] = "Meta API Error ({$errorCode}): {$errorMsg}";

                if (str_contains($errorMsg, 'Invalid parameter')) {
                    $result['hint'] = 'Test WABA can only send to verified test numbers. Add your phone in Meta Dashboard > WhatsApp > API Setup > To.';
                } elseif (str_contains($errorMsg, 'OAuthException') || str_contains($errorMsg, 'access token')) {
                    $result['hint'] = 'Access token is invalid or expired. Generate a new permanent token in Meta Business Manager > System Users.';
                } elseif (str_contains($errorMsg, 'Phone number')) {
                    $result['hint'] = 'Phone Number ID is incorrect. Check it in Meta Dashboard > WhatsApp > API Setup.';
                }
            }

            // Log to whatsapp_messages if we have an order
            if ($orderId > 0) {
                $this->logMessage($orderId, $type, $payload['text']['body'] ?? $payload['interactive']['body']['text'] ?? '', $phone, $result);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('WhatsApp API Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Log message to database
     */
    protected function logMessage(int $orderId, string $type, string $message, string $phone, array $result): void
    {
        try {
            // Find or create conversation
            $order = Order::find($orderId);
            if (!$order) return;

            $conversation = \DB::table('whatsapp_conversations')
                ->where('tenant_id', tenant('id'))
                ->where('order_id', $orderId)
                ->first();

            if (!$conversation) {
                $convId = \DB::table('whatsapp_conversations')->insertGetId([
                    'tenant_id' => tenant('id'),
                    'order_id' => $orderId,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $this->formatPhone($order->customer_phone),
                    'status' => 'open',
                    'last_message_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $convId = $conversation->id;
                \DB::table('whatsapp_conversations')->where('id', $convId)->update([
                    'last_message_at' => now(),
                ]);
            }

            \DB::table('whatsapp_messages')->insert([
                'conversation_id' => $convId,
                'tenant_id' => tenant('id'),
                'direction' => 'outbound',
                'message_type' => $type,
                'message_body' => $message,
                'to_phone' => $phone,
                'from_phone' => $this->phoneNumberId,
                'status' => $result['success'] ? 'sent' : 'failed',
                'provider_message_id' => $result['response']['messages'][0]['id'] ?? null,
                'provider_response' => json_encode($result['response'] ?? []),
                'is_auto' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp log failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle incoming webhook (customer confirm/cancel button click or incoming messages)
     */
    public function handleWebhook(array $payload): array
    {
        try {
            $entry = $payload['entry'][0] ?? null;
            if (!$entry) return ['status' => 'no_entry'];

            $changes = $entry['changes'][0] ?? null;
            if (!$changes) return ['status' => 'no_changes'];

            $value = $changes['value'] ?? [];
            $messages = $value['messages'] ?? [];

            foreach ($messages as $message) {
                $from = $message['from'] ?? '';

                // Handle interactive button clicks
                if (($message['type'] ?? '') === 'interactive') {
                    $interactive = $message['interactive'];
                    $buttonId = $interactive['button']['id'] ?? '';

                    if (preg_match('/^(confirm|cancel)_(\d+)$/', $buttonId, $matches)) {
                        $action = $matches[1];
                        $orderId = (int) $matches[2];

                        $order = Order::find($orderId);
                        if ($order) {
                            $newStatus = $action === 'confirm' ? 'processing' : 'cancelled';
                            $order->update(['status' => $newStatus]);

                            if ($action === 'confirm') {
                                $this->sendOrderConfirmed($order);
                            } else {
                                $this->sendOrderCancelled($order, 'Cancelled by customer via WhatsApp');
                            }

                            return ['status' => 'processed', 'order_id' => $orderId, 'action' => $action];
                        }
                    }
                }

                // Handle regular text messages (incoming chat)
                if (($message['type'] ?? '') === 'text') {
                    $text = $message['text']['body'] ?? '';
                    $this->handleIncomingMessage($from, $text);
                }
            }

            return ['status' => 'no_action'];
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle incoming customer message
     */
    protected function handleIncomingMessage(string $fromPhone, string $text): void
    {
        $formattedPhone = $this->formatPhone($fromPhone);

        // Find conversation by phone number
        $conversation = \DB::table('whatsapp_conversations')
            ->where('tenant_id', tenant('id'))
            ->where('customer_phone', $formattedPhone)
            ->orderByDesc('last_message_at')
            ->first();

        if ($conversation) {
            // Store incoming message
            \DB::table('whatsapp_messages')->insert([
                'conversation_id' => $conversation->id,
                'tenant_id' => tenant('id'),
                'direction' => 'inbound',
                'message_type' => 'text',
                'message_body' => $text,
                'from_phone' => $formattedPhone,
                'to_phone' => $this->phoneNumberId,
                'status' => 'received',
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
        }
    }
}
