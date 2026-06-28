<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DomainService
{
    protected string $platformDomain;
    protected string $platformIp;
    protected ?string $renderApiKey;
    protected ?string $renderServiceId;

    public function __construct()
    {
        $this->platformDomain = config('platform.domain', 'saas-ecommerce-xx7e.onrender.com');
        $this->platformIp = config('platform.ip', '');
        $this->renderApiKey = config('services.render.api_key', env('RENDER_API_KEY'));
        $this->renderServiceId = config('services.render.service_id', env('RENDER_SERVICE_ID'));
    }

    /**
     * Check DNS status for a domain
     * Returns: 'connected', 'resolving', 'mismatch', 'pending'
     */
    public function checkDnsStatus(string $domain): string
    {
        $cacheKey = "dns_check_{$domain}";
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Localhost always connected (dev only)
        if (str_ends_with($domain, 'localhost') || $domain === '127.0.0.1') {
            Cache::put($cacheKey, 'connected', 300);
            return 'connected';
        }

        // Check if domain resolves
        $ip = gethostbyname($domain);
        if ($ip === $domain) {
            // DNS not propagated yet
            Cache::put($cacheKey, 'resolving', 60);
            return 'resolving';
        }

        // Check A record
        if (!empty($this->platformIp) && $ip === $this->platformIp) {
            Cache::put($cacheKey, 'connected', 300);
            return 'connected';
        }

        // Check CNAME
        $cname = @dns_get_record($domain, DNS_CNAME);
        if (!empty($cname) && str_ends_with($cname[0]['target'], $this->platformDomain)) {
            Cache::put($cacheKey, 'connected', 300);
            return 'connected';
        }

        // Check if it resolves to a valid IP (might be pointing elsewhere)
        if ($ip && $ip !== $domain) {
            Cache::put($cacheKey, 'mismatch', 300);
            return 'mismatch';
        }

        Cache::put($cacheKey, 'resolving', 60);
        return 'resolving';
    }

    /**
     * Check if a domain is verified in our system
     */
    public function isDomainVerified(string $domain): bool
    {
        return $this->checkDnsStatus($domain) === 'connected';
    }

    /**
     * Get DNS records needed for domain setup
     */
    public function getDnsInstructions(string $domain): array
    {
        $isRoot = !str_contains($domain, '.' . $this->platformDomain) 
                  && substr_count($domain, '.') === 1;

        $instructions = [
            'domain' => $domain,
            'platform_domain' => $this->platformDomain,
            'platform_ip' => $this->platformIp,
            'records' => [],
            'render_steps' => [],
        ];

        // Render dashboard steps (always required first)
        $instructions['render_steps'] = [
            'title' => 'Add Domain in Render Dashboard',
            'steps' => [
                'Go to <strong>Render Dashboard</strong> → your web service → <strong>Settings</strong>',
                'Scroll to <strong>Custom Domains</strong> → click <strong>Add Custom Domain</strong>',
                "Enter your domain: <code>{$domain}</code>",
                'Click <strong>Save</strong> — Render will show you the DNS records below',
                'Copy the exact DNS records Render shows you and add them at your domain registrar',
            ],
            'note' => 'Render automatically provisions an SSL certificate once DNS is configured correctly.',
        ];

        // DNS records to add at registrar
        if ($isRoot) {
            // Root domain (mybrand.com) - needs A record
            $instructions['records'][] = [
                'type' => 'A',
                'host' => '@',
                'value' => $this->platformIp ?: 'Add via Render Dashboard',
                'ttl' => 'Auto',
                'description' => 'Points your root domain to our servers',
                'required' => true,
            ];
            $instructions['records'][] = [
                'type' => 'CNAME',
                'host' => 'www',
                'value' => $this->platformDomain,
                'ttl' => 'Auto',
                'description' => 'Points www subdomain to our platform',
                'required' => true,
            ];
        } else {
            // Subdomain (store.mybrand.com) - needs CNAME
            $instructions['records'][] = [
                'type' => 'CNAME',
                'host' => $domain,
                'value' => $this->platformDomain,
                'ttl' => 'Auto',
                'description' => 'Points your subdomain to our platform',
                'required' => true,
            ];
        }

        return $instructions;
    }

    /**
     * Add domain to Render via API
     */
    public function addToRender(string $domain): array
    {
        if (empty($this->renderApiKey) || empty($this->renderServiceId)) {
            return [
                'success' => false,
                'message' => 'Render API not configured. Add domain manually in Render dashboard.',
                'manual' => true,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->renderApiKey}",
                'Accept' => 'application/json',
            ])->post("https://api.render.com/v1/services/{$this->renderServiceId}/custom-domains", [
                'name' => $domain,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Domain added to Render successfully.',
                ];
            }

            $error = $response->json('message', 'Unknown error');
            return [
                'success' => false,
                'message' => "Render API error: {$error}",
            ];
        } catch (\Exception $e) {
            Log::error('Render API error', ['domain' => $domain, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Failed to connect to Render API. Add domain manually.',
                'manual' => true,
            ];
        }
    }

    /**
     * Remove domain from Render via API
     */
    public function removeFromRender(string $domain): array
    {
        if (empty($this->renderApiKey) || empty($this->renderServiceId)) {
            return ['success' => false, 'message' => 'Render API not configured.'];
        }

        try {
            // First, find the domain ID
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->renderApiKey}",
                'Accept' => 'application/json',
            ])->get("https://api.render.com/v1/services/{$this->renderServiceId}/custom-domains");

            if ($response->successful()) {
                $domains = $response->json('items', []);
                foreach ($domains as $d) {
                    if (($d['domain'] ?? '') === $domain) {
                        $domainId = $d['id'];
                        $deleteResponse = Http::withHeaders([
                            'Authorization' => "Bearer {$this->renderApiKey}",
                            'Accept' => 'application/json',
                        ])->delete("https://api.render.com/v1/services/{$this->renderServiceId}/custom-domains/{$domainId}");

                        return $deleteResponse->successful()
                            ? ['success' => true, 'message' => 'Domain removed from Render.']
                            : ['success' => false, 'message' => 'Failed to remove domain from Render.'];
                    }
                }
            }

            return ['success' => false, 'message' => 'Domain not found in Render.'];
        } catch (\Exception $e) {
            Log::error('Render API error', ['domain' => $domain, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Failed to connect to Render API.'];
        }
    }

    /**
     * Get Render custom domains status
     */
    public function getRenderDomains(): array
    {
        if (empty($this->renderApiKey) || empty($this->renderServiceId)) {
            return ['configured' => false, 'domains' => []];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->renderApiKey}",
                'Accept' => 'application/json',
            ])->get("https://api.render.com/v1/services/{$this->renderServiceId}/custom-domains");

            if ($response->successful()) {
                return [
                    'configured' => true,
                    'domains' => $response->json('items', []),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Render API error', ['error' => $e->getMessage()]);
        }

        return ['configured' => true, 'domains' => []];
    }

    /**
     * Validate domain format
     */
    public function validateDomain(string $domain): ?string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/^https?:\/\//i', '', $domain);
        $domain = rtrim($domain, '/');

        if (empty($domain)) {
            return null;
        }

        // Basic format validation
        if (!preg_match('/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9\-]*[a-z0-9])?)*\.[a-z]{2,}$/', $domain)) {
            return null;
        }

        // Check it's not a central domain
        $centralDomains = config('tenancy.central_domains', []);
        if (in_array($domain, $centralDomains)) {
            return null;
        }

        // Check it's not the platform domain
        if ($domain === $this->platformDomain) {
            return null;
        }

        return $domain;
    }
}
