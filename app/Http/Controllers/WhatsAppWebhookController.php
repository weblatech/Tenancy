<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWhatsAppWebhook;
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
     *
     * Meta sends:
     *   - hub_mode=subscribe
     *   - hub_verify_token=<your verify token>
     *   - hub_challenge=<random string to echo back>
     *
     * We validate the verify_token against:
     *  1. The store's whatsapp_verify_token (store_settings table)
     *  2. The central provider's verify_token (whatsapp_providers table)
     */
    public function verify(Request $request, string $tenantId): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        Log::info('WhatsApp webhook verification attempt', [
            'store_id' => $tenantId,
            'mode' => $mode,
            'token' => $token,
        ]);

        if ($mode !== 'subscribe') {
            Log::warning('WhatsApp verify: invalid mode', ['mode' => $mode]);
            return response('Forbidden', 403);
        }

        // Check token against store settings and central provider
        $service = new WhatsAppService($tenantId);
        $validToken = $service->getVerifyToken();

        if (empty($validToken)) {
            Log::warning('WhatsApp verify: no verify token configured', ['store_id' => $tenantId]);
            return response('Forbidden', 403);
        }

        if ($token === $validToken) {
            Log::info('WhatsApp verify: success', ['store_id' => $tenantId]);
            return response((string) $challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        Log::warning('WhatsApp verify: token mismatch', [
            'store_id' => $tenantId,
            'received' => $token,
            'expected' => $validToken,
        ]);

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming webhook events from Meta.
     *
     * POST /webhook/whatsapp/{tenantId}
     *
     * This receives:
     *  - Button clicks (confirm_X / cancel_X)
     *  - Incoming text messages
     *  - Status updates (delivered, read, failed)
     *  - Media messages
     *
     * The payload is dispatched to a queued job for processing.
     */
    public function handle(Request $request, string $tenantId): Response
    {
        $payload = $request->all();

        Log::info('WhatsApp webhook received', [
            'store_id' => $tenantId,
            'method' => $request->method(),
            'has_entry' => isset($payload['entry']),
        ]);

        // Validate payload structure
        if (!isset($payload['entry'][0]['changes'][0])) {
            Log::warning('WhatsApp webhook: invalid payload structure', ['store_id' => $tenantId]);
            return response('OK', 200);
        }

        // Dispatch to queue for async processing
        ProcessWhatsAppWebhook::dispatch($tenantId, $payload);

        // Return 200 immediately to acknowledge receipt (Meta requires fast response)
        return response('OK', 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Handle incoming messages via the alternative incoming webhook endpoint.
     *
     * POST /webhook/whatsapp/{tenantId}/incoming
     *
     * This is an alternative endpoint for stores that need a separate
     * incoming message handler.
     */
    public function incoming(Request $request, string $tenantId): Response
    {
        $payload = $request->all();

        Log::info('WhatsApp incoming webhook', [
            'store_id' => $tenantId,
            'payload_keys' => array_keys($payload),
        ]);

        if (!isset($payload['entry'][0]['changes'][0])) {
            return response('OK', 200);
        }

        // Dispatch to queue
        ProcessWhatsAppWebhook::dispatch($tenantId, $payload);

        return response('OK', 200)
            ->header('Content-Type', 'text/plain');
    }
}
