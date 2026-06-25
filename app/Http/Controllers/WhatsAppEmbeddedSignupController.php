<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use App\Services\WhatsAppRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsAppEmbeddedSignupController extends Controller
{
    /**
     * Show the Embedded Signup page for this tenant.
     */
    public function showPage()
    {
        $tenantId = tenant('id');
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        $phoneNumberId = $settings->whatsapp_phone_number_id ?? '';

        return view('tenant.whatsapp-crm.register', [
            'tenantId' => $tenantId,
            'phoneNumberId' => $phoneNumberId,
        ]);
    }

    /**
     * Handle the FB SDK callback after Embedded Signup completes.
     *
     * Flow:
     * 1. Receive short-lived accessToken from the FB JS SDK
     * 2. Exchange it for a long-lived token via Meta Graph API
     * 3. Fetch WABA and phone numbers associated with the token
     * 4. Save credentials to the tenant's store_settings table
     * 5. Return phone numbers so the user can select one
     */
    public function connectStore(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        $shortToken = $request->input('access_token');
        $appId = config('services.meta.app_id');
        $appSecret = config('services.meta.app_secret');

        if (empty($appId) || empty($appSecret)) {
            return response()->json([
                'success' => false,
                'error' => 'Meta app credentials not configured. Ask your admin to set META_APP_ID and META_APP_SECRET.',
            ], 422);
        }

        // Step 1: Exchange short-lived token for long-lived token
        $longToken = $this->exchangeForLongLivedToken($shortToken, $appId, $appSecret);
        if ($longToken === null) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to exchange access token. The token may be invalid or expired.',
            ], 422);
        }

        // Step 2: Fetch WABA and phone numbers using the long-lived token
        $wabaData = $this->fetchWabaAndPhoneNumbers($longToken);
        if ($wabaData === null) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch WhatsApp Business accounts. Ensure your Facebook account has a WABA.',
            ], 422);
        }

        // Step 3: Save credentials to store_settings
        $this->saveCredentials($longToken, $wabaData);

        Log::info('WhatsApp Embedded Signup: Connected', [
            'tenant_id' => tenant('id'),
            'waba_id' => $wabaData['waba_id'] ?? null,
            'phone_count' => count($wabaData['phone_numbers'] ?? []),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp connected successfully',
            'phone_numbers' => $wabaData['phone_numbers'] ?? [],
            'waba_id' => $wabaData['waba_id'] ?? null,
            'waba_name' => $wabaData['waba_name'] ?? null,
        ]);
    }

    /**
     * Exchange a short-lived FB access token for a long-lived one.
     *
     * @see https://developers.facebook.com/docs/facebook-login/guides/access-tokens/expiration-and-extension
     */
    protected function exchangeForLongLivedToken(string $shortToken, string $appId, string $appSecret): ?string
    {
        try {
            $response = Http::get('https://graph.facebook.com/v18.0/oauth/access_token', [
                'grant_type' => 'fb_exchange_token',
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'fb_exchange_token' => $shortToken,
            ]);

            if ($response->failed()) {
                Log::error('Token exchange failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json('access_token');
        } catch (\Exception $e) {
            Log::error('Token exchange exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Fetch WhatsApp Business Accounts and phone numbers for the given token.
     *
     * Uses the /me edge to discover the user's WABAs, then fetches phone numbers
     * for the first WABA found.
     */
    protected function fetchWabaAndPhoneNumbers(string $longToken): ?array
    {
        try {
            // Step 1: Get WABAs belonging to this user
            $wabaResponse = Http::withToken($longToken)->get(
                'https://graph.facebook.com/v18.0/me',
                ['fields' => 'whatsapp_business_accounts{id,name}']
            );

            if ($wabaResponse->failed()) {
                Log::error('WABA fetch failed', [
                    'status' => $wabaResponse->status(),
                    'body' => $wabaResponse->body(),
                ]);
                return null;
            }

            $wabas = $wabaResponse->json('whatsapp_business_accounts.data', []);

            if (empty($wabas)) {
                Log::warning('No WABAs found for user', ['tenant_id' => tenant('id')]);
                return [
                    'waba_id' => null,
                    'waba_name' => null,
                    'phone_numbers' => [],
                ];
            }

            // Step 2: Use the first WABA (most users have one)
            $waba = $wabas[0];
            $wabaId = $waba['id'];
            $wabaName = $waba['name'] ?? '';

            // Step 3: Fetch phone numbers for this WABA
            $phoneResponse = Http::withToken($longToken)->get(
                "https://graph.facebook.com/v18.0/{$wabaId}",
                ['fields' => 'phone_numbers{id,display_phone_number,verified_name,status,quality_rating}']
            );

            $phoneNumbers = [];
            if ($phoneResponse->successful()) {
                $phoneNumbers = $phoneResponse->json('phone_numbers.data', []);
            }

            return [
                'waba_id' => $wabaId,
                'waba_name' => $wabaName,
                'phone_numbers' => $phoneNumbers,
            ];
        } catch (\Exception $e) {
            Log::error('WABA fetch exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Save the long-lived token, WABA ID, and generate a verify token.
     *
     * The access token is encrypted before storage.
     * The verify token is a random string used for webhook verification.
     */
    protected function saveCredentials(string $longToken, array $wabaData): void
    {
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        $tenantId = tenant('id');

        // Generate a unique verify token if one doesn't exist
        $verifyToken = $settings->whatsapp_verify_token ?? Str::random(32);

        // Encrypt the long-lived token before saving
        $encryptedToken = Crypt::encryptString($longToken);

        $settings->update([
            'whatsapp_api_key' => $encryptedToken,
            'whatsapp_verify_token' => $verifyToken,
            'whatsapp_crm_active' => true,
            'whatsapp_webhook_url' => url('/webhook/whatsapp/universal'),
        ]);

        // Also update the central whatsapp_providers table if this is the first setup
        $existingProvider = \DB::connection(config('tenancy.database.central_connection'))
            ->table('whatsapp_providers')
            ->where('is_active', true)
            ->first();

        if (!$existingProvider) {
            \DB::connection(config('tenancy.database.central_connection'))
                ->table('whatsapp_providers')->insert([
                    'provider_name' => 'meta',
                    'api_key' => $encryptedToken,
                    'phone_number_id' => '',
                    'business_account_id' => $wabaData['waba_id'] ?? '',
                    'verify_token' => $verifyToken,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
    }
}
