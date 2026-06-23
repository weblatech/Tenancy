<?php

namespace App\Services;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected StoreSetting $settings;
    protected string $apiKey;
    protected string $phoneNumberId;
    protected string $verifyToken;
    protected string $storeId;
    protected string $baseUrl = 'https://graph.facebook.com/v18.0';

    public function __construct(?string $storeId = null)
    {
        $this->storeId = $storeId ?? tenant('id');
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
     * Check if WhatsApp is fully configured for this store
     */
    public function isConfigured(): bool
    {
        $provider = self::getProvider();
        return $provider
            && !empty($this->settings->whatsapp_crm_active)
            && !empty($this->getPhoneNumberId())
            && !empty($this->getApiKey());
    }

    /**
     * Get API key: tenant setting → central provider
     */
    public function getApiKey(): string
    {
        if (!empty($this->apiKey)) {
            return $this->apiKey;
        }
        $provider = self::getProvider();
        return $provider->api_key ?? '';
    }

    /**
     * Get Phone Number ID: tenant setting → central provider
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
     * Get the store's verify token: tenant setting → central provider
     */
    public function getVerifyToken(): string
    {
        if (!empty($this->verifyToken)) {
            return $this->verifyToken;
        }
        $provider = self::getProvider();
        return $provider->verify_token ?? '';
    }

    /**
     * Format phone number to E.164 format
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

        return $phone;
    }

    /**
     * Build order summary text for messages
     */
    public function getOrderSummary(Order $order): string
    {
        $items = $order->cart_items ?? [];
        $lines = '';
        foreach ($items as $item) {
            $name = $item['name'] ?? $item['product_name'] ?? 'Product';
            $qty = $item['quantity'] ?? 1;
            $price = $item['price'] ?? 0;
            $lines .= "- {$name} x{$qty} = Rs. " . number_format($price * $qty) . "\n";
        }
        return $lines;
    }

    /**
     * Build message from custom template with variable replacement
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
     * Send a template message with dynamic variables.
     *
     * @param  string  $storeId       Tenant ID
     * @param  string  $templateName  Meta template name (e.g. 'order_confirmation')
     * @param  string  $customerPhone Customer phone in any format
     * @param  array   $variables     Template variables ['param1_value', 'param2_value']
     * @param  int     $orderId       Related order ID for logging
     * @return array   ['success' => bool, 'response' => array, 'error' => string|null]
     */
    public function sendTemplateMessage(
        string $storeId,
        string $templateName,
        string $customerPhone,
        array $variables = [],
        int $orderId = 0
    ): array {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp API not configured', 'response' => []];
        }

        $phone = $this->formatPhone($customerPhone);
        $phoneId = $this->getPhoneNumberId();

        // Build components array for template variables
        $components = [];
        if (!empty($variables)) {
            $parameters = array_map(fn($v) => ['type' => 'text', 'text' => (string) $v], $variables);
            $components[] = [
                'type' => 'body',
                'parameters' => $parameters,
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => 'en_US'],
                'components' => $components,
            ],
        ];

        return $this->sendRequest($payload, $orderId, "template_{$templateName}", $phone);
    }

    /**
     * Send a plain text message
     */
    public function sendTextMessage(string $phone, string $text, int $orderId = 0, string $type = 'text'): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp API not configured', 'response' => []];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhone($phone),
            'type' => 'text',
            'text' => ['body' => $text],
        ];

        return $this->sendRequest($payload, $orderId, $type, $this->formatPhone($phone));
    }

    /**
     * Send interactive message with buttons
     */
    public function sendInteractiveMessage(string $phone, string $text, array $buttons, int $orderId = 0, string $type = 'interactive'): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp API not configured', 'response' => []];
        }

        $formattedPhone = $this->formatPhone($phone);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $formattedPhone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => ['text' => $text],
                'action' => ['buttons' => $buttons],
            ],
        ];

        return $this->sendRequest($payload, $orderId, $type, $formattedPhone);
    }

    /**
     * Send order pending confirmation (interactive with Confirm/Cancel buttons)
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
    public function sendChatMessage(string $phone, string $message, int $conversationId = 0): array
    {
        return $this->sendTextMessage($phone, $message, 0, 'manual_chat');
    }

    /**
     * Execute the HTTP request to WhatsApp Graph API and log result
     */
    protected function sendRequest(array $payload, int $orderId, string $type, string $phone): array
    {
        try {
            $apiKey = $this->getApiKey();
            $phoneId = $this->getPhoneNumberId();

            if (empty($apiKey)) {
                $error = 'API Key (Access Token) is empty. Save it in Super Admin > WhatsApp Provider.';
                $this->logToWhatsappLogs($orderId, $type, $payload, $phone, false, $error);
                return ['success' => false, 'error' => $error, 'response' => []];
            }
            if (empty($phoneId)) {
                $error = 'Phone Number ID is empty. Save it in CRM Settings.';
                $this->logToWhatsappLogs($orderId, $type, $payload, $phone, false, $error);
                return ['success' => false, 'error' => $error, 'response' => []];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post(
                "{$this->baseUrl}/{$phoneId}/messages",
                $payload
            );

            $body = $response->json();
            $success = $response->successful();

            $result = [
                'success' => $success,
                'status_code' => $response->status(),
                'response' => $body,
            ];

            if (!$success) {
                $errorMsg = $body['error']['message'] ?? 'Unknown error';
                $errorCode = $body['error']['code'] ?? 0;
                $result['error'] = "Meta API Error ({$errorCode}): {$errorMsg}";

                Log::error('WhatsApp API Error', [
                    'store_id' => $this->storeId,
                    'status' => $response->status(),
                    'error_code' => $errorCode,
                    'error_message' => $errorMsg,
                    'phone' => $phone,
                    'phone_id' => $phoneId,
                ]);

                if (str_contains($errorMsg, 'Invalid parameter')) {
                    $result['hint'] = 'Test WABA can only send to verified test numbers. Add your phone in Meta Dashboard > WhatsApp > API Setup > To.';
                } elseif (str_contains($errorMsg, 'OAuthException') || str_contains($errorMsg, 'access token')) {
                    $result['hint'] = 'Access token is invalid or expired. Generate a new permanent token in Meta Business Manager > System Users.';
                } elseif (str_contains($errorMsg, 'Phone number')) {
                    $result['hint'] = 'Phone Number ID is incorrect. Check it in Meta Dashboard > WhatsApp > API Setup.';
                }
            }

            // Log to whatsapp_logs table
            $messageBody = $payload['text']['body']
                ?? $payload['interactive']['body']['text']
                ?? $payload['template']['name']
                ?? '';
            $this->logToWhatsappLogs($orderId, $type, $messageBody, $phone, $success, json_encode($body));

            // Also log to whatsapp_messages for chat panel
            if ($orderId > 0) {
                $this->logToConversation($orderId, $type, $messageBody, $phone, $success, $body);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('WhatsApp API Exception: ' . $e->getMessage(), ['store_id' => $this->storeId]);
            $this->logToWhatsappLogs($orderId, $type, $payload['text']['body'] ?? '', $phone, false, $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage(), 'response' => []];
        }
    }

    /**
     * Log message to whatsapp_logs table (linked to order)
     */
    protected function logToWhatsappLogs(
        int $orderId,
        string $type,
        mixed $payload,
        string $phone,
        bool $success,
        string $providerResponse
    ): void {
        try {
            $messageBody = is_string($payload)
                ? $payload
                : ($payload['text']['body'] ?? $payload['interactive']['body']['text'] ?? json_encode($payload));

            \DB::table('whatsapp_logs')->insert([
                'order_id' => $orderId ?: 0,
                'tenant_id' => $this->storeId,
                'direction' => 'outbound',
                'message_type' => $type,
                'message_body' => $messageBody,
                'to_phone' => $phone,
                'from_phone' => $this->phoneNumberId,
                'status' => $success ? 'sent' : 'failed',
                'provider_message_id' => null,
                'provider_response' => $providerResponse,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp log insert failed: ' . $e->getMessage());
        }
    }

    /**
     * Log message to whatsapp_messages + whatsapp_conversations (for chat panel)
     */
    protected function logToConversation(
        int $orderId,
        string $type,
        string $message,
        string $phone,
        bool $success,
        array $response
    ): void {
        try {
            $order = Order::find($orderId);
            if (!$order) return;

            $conversation = \DB::table('whatsapp_conversations')
                ->where('tenant_id', $this->storeId)
                ->where('order_id', $orderId)
                ->first();

            if (!$conversation) {
                $convId = \DB::table('whatsapp_conversations')->insertGetId([
                    'tenant_id' => $this->storeId,
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
                'tenant_id' => $this->storeId,
                'direction' => 'outbound',
                'message_type' => $type,
                'message_body' => $message,
                'to_phone' => $phone,
                'from_phone' => $this->phoneNumberId,
                'status' => $success ? 'sent' : 'failed',
                'provider_message_id' => $response['messages'][0]['id'] ?? null,
                'provider_response' => json_encode($response),
                'is_auto' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp conversation log failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle incoming webhook payload (button clicks + text messages)
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
                    $buttonId = $message['interactive']['button']['id'] ?? '';

                    if (preg_match('/^(confirm|cancel)_(\d+)$/', $buttonId, $matches)) {
                        $action = $matches[1];
                        $orderId = (int) $matches[2];

                        $order = Order::find($orderId);
                        if ($order) {
                            $newStatus = $action === 'confirm' ? 'processing' : 'cancelled';
                            $order->update(['status' => $newStatus]);

                            // Log the inbound button click
                            $this->logInboundMessage($from, "Customer clicked: {$action}", $orderId);

                            if ($action === 'confirm') {
                                $this->sendOrderConfirmed($order);
                            } else {
                                $this->sendOrderCancelled($order, 'Cancelled by customer via WhatsApp');
                            }

                            return [
                                'status' => 'processed',
                                'order_id' => $orderId,
                                'action' => $action,
                                'new_status' => $newStatus,
                            ];
                        }
                    }
                }

                // Handle regular text messages
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
     * Handle incoming customer text message
     */
    protected function handleIncomingMessage(string $fromPhone, string $text): void
    {
        $formattedPhone = $this->formatPhone($fromPhone);

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
                'message_type' => 'text',
                'message_body' => $text,
                'from_phone' => $formattedPhone,
                'to_phone' => $this->phoneNumberId,
                'status' => 'received',
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
     * Log an inbound message to whatsapp_logs
     */
    protected function logInboundMessage(string $fromPhone, string $message, int $orderId = 0): void
    {
        try {
            \DB::table('whatsapp_logs')->insert([
                'order_id' => $orderId,
                'tenant_id' => $this->storeId,
                'direction' => 'inbound',
                'message_type' => 'customer_action',
                'message_body' => $message,
                'to_phone' => $this->phoneNumberId,
                'from_phone' => $this->formatPhone($fromPhone),
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
}
