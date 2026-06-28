<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use App\Services\DomainService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DomainController extends Controller
{
    protected DomainService $domainService;

    public function __construct(DomainService $domainService)
    {
        $this->domainService = $domainService;
    }

    /**
     * Show domain management page
     */
    public function index(Request $request)
    {
        $tenantId = tenant('id');
        $domains = tenant()->domains()->get();
        $platformDomain = config('platform.domain', 'saas-ecommerce-xx7e.onrender.com');
        $platformIp = config('platform.ip', '');
        $defaultSubdomain = $tenantId . '.' . $platformDomain;

        // Check DNS status for each domain
        $domainStatuses = [];
        foreach ($domains as $domain) {
            $domainStatuses[$domain->domain] = $this->domainService->checkDnsStatus($domain->domain);
        }

        // Check Render API status
        $renderStatus = $this->domainService->getRenderDomains();

        // Determine current store URL
        $currentHost = $request->getHost();
        $storeUrl = "https://{$currentHost}";

        return view('tenant.domains.index', [
            'tenantId' => $tenantId,
            'domains' => $domains,
            'domainStatuses' => $domainStatuses,
            'platformIp' => $platformIp,
            'platformDomain' => $platformDomain,
            'defaultSubdomain' => $defaultSubdomain,
            'storeUrl' => $storeUrl,
            'renderStatus' => $renderStatus,
        ]);
    }

    /**
     * Add a custom domain
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $domain = $this->domainService->validateDomain($request->domain);

        if (!$domain) {
            return redirect()->back()->with('error', 'Invalid domain format. Use format: mybrand.com or store.mybrand.com');
        }

        // Check uniqueness across all tenants
        $exists = \Stancl\Tenancy\Database\Models\Domain::on(config('tenancy.database.central_connection'))
            ->where('domain', $domain)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This domain is already registered by another store.');
        }

        // Check it's not a default subdomain of another tenant
        $centralDomains = config('tenancy.central_domains', []);
        $platformDomain = config('platform.domain', 'saas-ecommerce-xx7e.onrender.com');
        foreach ($centralDomains as $cd) {
            if ($domain === $cd || $domain === $platformDomain) {
                return redirect()->back()->with('error', 'Cannot register a platform domain.');
            }
        }

        // Create domain in our DB
        tenant()->domains()->create(['domain' => $domain]);

        // Try to add to Render via API (if configured)
        $renderResult = $this->domainService->addToRender($domain);

        // Get DNS instructions
        $instructions = $this->domainService->getDnsInstructions($domain);

        $successMsg = 'Domain added successfully!';
        if (!empty($renderResult['success'])) {
            $successMsg .= ' It has been added to Render. Configure DNS to activate.';
        } elseif (!empty($renderResult['manual'])) {
            $successMsg .= ' Now add it manually in Render Dashboard, then configure DNS.';
        }

        return redirect('/shop/domains')->with('success', $successMsg);
    }

    /**
     * Delete a custom domain
     */
    public function destroy($id, Request $request)
    {
        $domain = tenant()->domains()->findOrFail($id);

        $platformDomain = config('platform.domain', 'saas-ecommerce-xx7e.onrender.com');
        $defaultSubdomain = tenant('id') . '.' . $platformDomain;

        // Must have at least one domain
        $totalDomains = tenant()->domains()->count();
        if ($totalDomains <= 1) {
            return redirect()->back()->with('error', 'Cannot delete! A store must have at least one active domain.');
        }

        // Cannot delete the default system subdomain
        if ($domain->domain === $defaultSubdomain) {
            return redirect()->back()->with('error', 'Cannot delete the default system subdomain.');
        }

        // Try to remove from Render
        $this->domainService->removeFromRender($domain->domain);

        // Clear DNS cache
        \Illuminate\Support\Facades\Cache::forget("dns_check_{$domain->domain}");

        $domain->delete();

        return redirect('/shop/domains')->with('success', 'Domain deleted successfully!');
    }

    /**
     * Check DNS status for a specific domain (AJAX)
     */
    public function checkDns(Request $request)
    {
        $request->validate(['domain' => 'required|string']);

        $domain = $request->domain;
        $status = $this->domainService->checkDnsStatus($domain);
        $instructions = $this->domainService->getDnsInstructions($domain);

        return response()->json([
            'domain' => $domain,
            'status' => $status,
            'instructions' => $instructions,
        ]);
    }

    /**
     * Get DNS instructions for a domain (AJAX)
     */
    public function getInstructions(Request $request)
    {
        $request->validate(['domain' => 'required|string']);

        $instructions = $this->domainService->getDnsInstructions($request->domain);

        return response()->json($instructions);
    }

    /**
     * Force re-check all domains DNS status
     */
    public function refreshDns()
    {
        $domains = tenant()->domains()->get();
        $results = [];

        foreach ($domains as $domain) {
            // Clear cache first
            \Illuminate\Support\Facades\Cache::forget("dns_check_{$domain->domain}");

            $results[$domain->domain] = $this->domainService->checkDnsStatus($domain->domain);
        }

        return response()->json($results);
    }
}
