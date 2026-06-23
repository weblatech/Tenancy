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

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    /**
     * @var string  Tenant/store ID
     */
    protected string $storeId;

    /**
     * @var string  Message type: 'text', 'interactive', 'template',
     *              'order_pending', 'order_confirmed', 'order_processing',
     *              'order_completed', 'order_cancelled', 'chat'
     */
    protected string $type;

    /**
     * @var string  Customer phone number
     */
    protected string $phone;

    /**
     * @var string|null  Text message body (for text/interactive types)
     */
    protected ?string $message;

    /**
     * @var int  Related order ID (0 if none)
     */
    protected int $orderId;

    /**
     * @var array  Interactive buttons (for 'interactive' type)
     */
    protected array $buttons;

    /**
     * @var string|null  Template name (for 'template' type)
     */
    protected ?string $templateName;

    /**
     * @var array  Template variables (for 'template' type)
     */
    protected array $variables;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $storeId,
        string $type,
        string $phone,
        ?string $message = null,
        int $orderId = 0,
        array $buttons = [],
        ?string $templateName = null,
        array $variables = []
    ) {
        $this->storeId = $storeId;
        $this->type = $type;
        $this->phone = $phone;
        $this->message = $message;
        $this->orderId = $orderId;
        $this->buttons = $buttons;
        $this->templateName = $templateName;
        $this->variables = $variables;
    }

    /**
     * Create a job for sending order pending notification (with Confirm/Cancel buttons)
     */
    public static function orderPending(string $storeId, Order $order): static
    {
        return new static(
            storeId: $storeId,
            type: 'order_pending',
            phone: $order->customer_phone,
            orderId: $order->id
        );
    }

    /**
     * Create a job for sending order status notification
     */
    public static function orderStatus(string $storeId, Order $order, string $status): static
    {
        return new static(
            storeId: $storeId,
            type: "order_{$status}",
            phone: $order->customer_phone,
            orderId: $order->id
        );
    }

    /**
     * Create a job for sending a template message
     */
    public static function template(
        string $storeId,
        string $templateName,
        string $phone,
        array $variables = [],
        int $orderId = 0
    ): static {
        return new static(
            storeId: $storeId,
            type: 'template',
            phone: $phone,
            orderId: $orderId,
            templateName: $templateName,
            variables: $variables
        );
    }

    /**
     * Create a job for sending a text message
     */
    public static function text(string $storeId, string $phone, string $message, int $orderId = 0): static
    {
        return new static(
            storeId: $storeId,
            type: 'text',
            phone: $phone,
            message: $message,
            orderId: $orderId
        );
    }

    /**
     * Create a job for sending a chat message
     */
    public static function chat(string $storeId, string $phone, string $message): static
    {
        return new static(
            storeId: $storeId,
            type: 'chat',
            phone: $phone,
            message: $message
        );
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Initialize tenancy for this store
        if (!tenancy()->initialized) {
            tenancy()->initialize(
                \App\Models\Tenant::find($this->storeId)
            );
        }

        $service = new WhatsAppService($this->storeId);

        if (!$service->isConfigured()) {
            Log::warning('WhatsApp not configured', ['store_id' => $this->storeId]);
            return;
        }

        $result = match ($this->type) {
            'order_pending' => $this->sendOrderPending($service),
            'order_confirmed' => $this->sendOrderStatus($service, 'confirmed'),
            'order_processing' => $this->sendOrderStatus($service, 'processing'),
            'order_completed' => $this->sendOrderStatus($service, 'completed'),
            'order_cancelled' => $this->sendOrderStatus($service, 'cancelled'),
            'template' => $service->sendTemplateMessage(
                $this->storeId,
                $this->templateName,
                $this->phone,
                $this->variables,
                $this->orderId
            ),
            'text' => $service->sendTextMessage($this->phone, $this->message, $this->orderId),
            'chat' => $service->sendChatMessage($this->phone, $this->message),
            default => ['success' => false, 'error' => "Unknown type: {$this->type}"],
        };

        if (!$result['success']) {
            Log::error('WhatsApp send failed', [
                'store_id' => $this->storeId,
                'type' => $this->type,
                'phone' => $this->phone,
                'order_id' => $this->orderId,
                'error' => $result['error'] ?? 'Unknown',
            ]);
        }
    }

    /**
     * Send order pending notification with interactive buttons
     */
    protected function sendOrderPending(WhatsAppService $service): array
    {
        $order = Order::find($this->orderId);
        if (!$order) {
            return ['success' => false, 'error' => 'Order not found'];
        }
        return $service->sendOrderPending($order);
    }

    /**
     * Send order status notification
     */
    protected function sendOrderStatus(WhatsAppService $service, string $status): array
    {
        $order = Order::find($this->orderId);
        if (!$order) {
            return ['success' => false, 'error' => 'Order not found'];
        }

        return match ($status) {
            'confirmed' => $service->sendOrderConfirmed($order),
            'processing' => $service->sendOrderProcessing($order),
            'completed' => $service->sendOrderCompleted($order),
            'cancelled' => $service->sendOrderCancelled($order),
            default => ['success' => false, 'error' => "Unknown status: {$status}"],
        };
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('WhatsApp job permanently failed', [
            'store_id' => $this->storeId,
            'type' => $this->type,
            'phone' => $this->phone,
            'order_id' => $this->orderId,
            'error' => $exception->getMessage(),
        ]);
    }
}
