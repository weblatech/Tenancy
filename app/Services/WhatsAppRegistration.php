<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppRegistration
{
    protected $accessToken;
    protected $businessAccountId;
    protected $baseUrl = 'https://graph.facebook.com/v18.0';

    public function __construct()
    {
        $provider = WhatsAppCRM::getProvider();
        if ($provider) {
            $this->accessToken = $provider->api_key ?? '';
            $this->businessAccountId = $provider->business_account_id ?? '';
        }
    }

    /**
     * Check if registration is possible (provider configured)
     */
    public function isReady(): bool
    {
        return !empty($this->accessToken) && !empty($this->businessAccountId);
    }

    /**
     * Get access token
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Get business account ID
     */
    public function getBusinessAccountId(): string
    {
        return $this->businessAccountId;
    }

    /**
     * List all phone numbers in the WhatsApp Business Account
     * Returns existing numbers so store owner can pick one or register new
     */
    public function listPhoneNumbers(): array
    {
        if (!$this->isReady()) {
            return ['success' => false, 'error' => 'WhatsApp Business Account not configured by admin'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
            ])->timeout(30)->get(
                "{$this->baseUrl}/{$this->businessAccountId}/phone_numbers"
            );

            $data = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'phone_numbers' => $data['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => $data['error']['message'] ?? 'Failed to list phone numbers',
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp list phone numbers error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Register a new phone number with the WhatsApp Business Account
     * This sends an OTP to the phone number
     *
     * @param string $phoneNumber Phone number in E.164 format (e.g., 923001234567)
     * @param string $friendlyName Display name for the phone number
     */
    public function registerPhoneNumber(string $phoneNumber, string $friendlyName = ''): array
    {
        if (!$this->isReady()) {
            return ['success' => false, 'error' => 'WhatsApp Business Account not configured by admin'];
        }

        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        try {
            $payload = [
                'cc' => substr($phoneNumber, 0, 2),
                'phone_number' => substr($phoneNumber, 2),
                'verified_name' => $friendlyName ?: 'Store WhatsApp',
                'method' => 'sms',
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post(
                "{$this->baseUrl}/{$this->businessAccountId}/phone_numbers",
                $payload
            );

            $data = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'phone_number_id' => $data['id'] ?? null,
                    'verification' => $data['verification'] ?? null,
                    'waba_id' => $this->businessAccountId,
                ];
            }

            return [
                'success' => false,
                'error' => $data['error']['message'] ?? 'Failed to register phone number',
                'error_code' => $data['error']['code'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp register phone error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verify OTP code for a phone number
     *
     * @param string $phoneNumberId The phone number ID from registerPhoneNumber
     * @param string $code The OTP code received via SMS
     */
    public function verifyPhoneNumber(string $phoneNumberId, string $code): array
    {
        if (!$this->isReady()) {
            return ['success' => false, 'error' => 'WhatsApp Business Account not configured by admin'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post(
                "{$this->baseUrl}/{$phoneNumberId}/verify",
                ['code' => $code]
            );

            $data = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Phone number verified successfully',
                    'phone_number_id' => $phoneNumberId,
                ];
            }

            return [
                'success' => false,
                'error' => $data['error']['message'] ?? 'Invalid verification code',
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp verify phone error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get phone number details after verification
     */
    public function getPhoneNumberDetails(string $phoneNumberId): array
    {
        if (!$this->isReady()) {
            return ['success' => false, 'error' => 'Not configured'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
            ])->timeout(30)->get(
                "{$this->baseUrl}/{$phoneNumberId}"
            );

            $data = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'phone_number' => $data['display_phone_number'] ?? '',
                    'verified_name' => $data['verified_name'] ?? '',
                    'quality_rating' => $data['quality_rating'] ?? '',
                    'status' => $data['status'] ?? '',
                ];
            }

            return ['success' => false, 'error' => 'Failed to get phone details'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Format phone number for Meta API (E.164)
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
}
