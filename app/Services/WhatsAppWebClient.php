<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppWebClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $port = env('WA_SERVER_PORT', 3001);
        $host = env('WA_SERVER_HOST', '127.0.0.1');
        $this->baseUrl = "http://{$host}:{$port}";
    }

    public function isServerRunning(): bool
    {
        try {
            $res = Http::timeout(3)->get("{$this->baseUrl}/api/health");
            return $res->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getStatus(string $tenantId): array
    {
        try {
            $res = Http::timeout(5)->get("{$this->baseUrl}/api/status/{$tenantId}");
            return $res->successful() ? $res->json() : [
                'status' => 'disconnected',
                'qr' => null,
                'phone' => null,
                'retries' => 0,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'server_offline',
                'qr' => null,
                'phone' => null,
                'retries' => 0,
            ];
        }
    }

    public function getQR(string $tenantId): array
    {
        try {
            $res = Http::timeout(5)->get("{$this->baseUrl}/api/qr/{$tenantId}");
            if ($res->successful()) {
                return $res->json();
            }
        } catch (\Exception $e) {}
        return ['qr' => null, 'status' => 'disconnected'];
    }

    public function startSession(string $tenantId): array
    {
        try {
            $res = Http::timeout(10)->post("{$this->baseUrl}/api/start/{$tenantId}");
            return $res->successful() ? $res->json() : ['success' => false, 'error' => 'Failed to start'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendMessage(string $tenantId, string $to, string $message): array
    {
        try {
            $res = Http::timeout(15)->post("{$this->baseUrl}/api/send/{$tenantId}", [
                'to' => $to,
                'message' => $message,
            ]);
            $body = $res->json();
            if ($res->successful()) {
                return [
                    'success' => true,
                    'message_id' => $body['messageId'] ?? null,
                    'timestamp' => $body['timestamp'] ?? null,
                ];
            }
            return ['success' => false, 'error' => $body['error'] ?? 'Send failed'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function logout(string $tenantId): array
    {
        try {
            $res = Http::timeout(10)->post("{$this->baseUrl}/api/logout/{$tenantId}");
            return $res->successful() ? ['success' => true] : ['success' => false];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function setWebhook(string $tenantId, string $url): array
    {
        try {
            $res = Http::timeout(5)->post("{$this->baseUrl}/api/webhook/{$tenantId}", [
                'url' => $url,
            ]);
            return $res->successful() ? ['success' => true] : ['success' => false];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
