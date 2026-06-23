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
     *
     * POST /webhook/whatsapp/{tenantId}
     *
     * Meta sends TWO types of payloads:
     *
     * 1. MESSAGES payload (customer sends a message):
     *    { "entry": [{ "changes": [{ "value": { "messages": [...], "metadata": {...} } }] }] }
     *
     * 2. STATUS payload (message status update):
     *    { "entry": [{ "changes": [{ "value": { "statuses": [...], "metadata": {...} } }] }] }
     */
    public function handle(Request $request, string $tenantId): Response
    {
        $payload = $request->all();

        // Log the FULL raw payload for debugging (first 2000 chars to avoid huge logs)
        Log::info('WhatsApp WEBHOOK POST received', [
            'store_id' => $tenantId,
            'payload_size' => strlen(json_encode($payload)),
            'payload_preview' => substr(json_encode($payload), 0, 2000),
        ]);

        // Extract metadata (contains phone_number_id, display_phone_number)
        $metadata = $payload['entry'][0]['changes'][0]['value']['metadata'] ?? null;
        if ($metadata) {
            Log::info('WhatsApp WEBHOOK metadata', [
                'store_id' => $tenantId,
                'phone_number_id' => $metadata['phone_number_id'] ?? null,
                'display_phone_number' => $metadata['display_phone_number'] ?? null,
            ]);
        }

        // Check for messages
        $messages = $payload['entry'][0]['changes'][0]['value']['messages'] ?? [];
        $statuses = $payload['entry'][0]['changes'][0]['value']['statuses'] ?? [];

        Log::info('WhatsApp WEBHOOK payload breakdown', [
            'store_id' => $tenantId,
            'has_messages' => count($messages) > 0,
            'message_count' => count($messages),
            'has_statuses' => count($statuses) > 0,
            'status_count' => count($statuses),
        ]);

        // Validate payload has something to process
        if (empty($messages) && empty($statuses)) {
            Log::info('WhatsApp WEBHOOK: no messages or statuses to process', ['store_id' => $tenantId]);
            return response('OK', 200);
        }

        // ── Process SYNCHRONOUSLY for reliability (queue can lose jobs on Render) ──
        // This ensures messages are never silently dropped
        try {
            $job = new ProcessWhatsAppWebhook($tenantId, $payload);
            $job->handle();
            Log::info('WhatsApp WEBHOOK: processed synchronously', ['store_id' => $tenantId]);
        } catch (\Exception $e) {
            Log::error('WhatsApp WEBHOOK: sync processing failed', [
                'store_id' => $tenantId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }

        // Also dispatch to queue as backup (for status updates, follow-ups, etc.)
        try {
            ProcessWhatsAppWebhook::dispatch($tenantId, $payload);
        } catch (\Exception $e) {
            Log::warning('WhatsApp WEBHOOK: queue dispatch failed (non-critical)', [
                'store_id' => $tenantId,
                'error' => $e->getMessage(),
            ]);
        }

        return response('OK', 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Handle incoming messages via the alternative incoming webhook endpoint.
     *
     * POST /webhook/whatsapp/{tenantId}/incoming
     */
    public function incoming(Request $request, string $tenantId): Response
    {
        $payload = $request->all();

        Log::info('WhatsApp INCOMING webhook', [
            'store_id' => $tenantId,
            'payload_preview' => substr(json_encode($payload), 0, 1500),
        ]);

        if (!isset($payload['entry'][0]['changes'][0])) {
            Log::warning('WhatsApp INCOMING: invalid payload structure', ['store_id' => $tenantId]);
            return response('OK', 200);
        }

        // Process synchronously
        try {
            $job = new ProcessWhatsAppWebhook($tenantId, $payload);
            $job->handle();
        } catch (\Exception $e) {
            Log::error('WhatsApp INCOMING: processing failed', [
                'store_id' => $tenantId,
                'error' => $e->getMessage(),
            ]);
        }

        return response('OK', 200)
            ->header('Content-Type', 'text/plain');
    }
}
