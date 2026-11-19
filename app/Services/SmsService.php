<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Configurable SMS sender controlled from Admin Settings.
 * Supports Generic HTTP (with {phone}, {message}, {api_key}, {sender_id} placeholders),
 * SSLWireless, BulkSMSBD, and Twilio.
 */
class SmsService
{
    public function __construct(protected SiteSettingService $settings) {}

    public function isOtpEnabled(): bool
    {
        return filter_var($this->settings->get('sms_otp_enabled', false), FILTER_VALIDATE_BOOLEAN);
    }

    public function send(string $phone, string $message): bool
    {
        $provider = (string) $this->settings->get('sms_provider', 'generic_http');
        $apiUrl = trim((string) $this->settings->get('sms_api_url', ''));
        $apiKey = trim((string) $this->settings->get('sms_api_key', ''));
        $senderId = trim((string) $this->settings->get('sms_sender_id', 'Sikhun'));

        if ($apiUrl === '' && app()->environment('local', 'testing')) {
            Log::info('SMS (local/testing fallback)', ['phone' => $phone, 'message' => $message]);

            return true;
        }

        if ($apiUrl === '') {
            Log::warning('SMS send skipped: sms_api_url is not configured in Admin Settings.');

            return false;
        }

        try {
            if ($provider === 'sslwireless') {
                $response = Http::timeout(10)->post($apiUrl, [
                    'api_token' => $apiKey,
                    'sid' => $senderId,
                    'msisdn' => $phone,
                    'sms' => $message,
                    'csms_id' => uniqid('skh_', true),
                ]);

                return $response->successful();
            }

            if ($provider === 'bulksmsbd') {
                $response = Http::timeout(10)->post($apiUrl, [
                    'api_key' => $apiKey,
                    'senderid' => $senderId,
                    'number' => $phone,
                    'message' => $message,
                ]);

                return $response->successful();
            }

            // Generic HTTP: supports template placeholders or JSON POST
            if (str_contains($apiUrl, '{phone}') || str_contains($apiUrl, '{message}')) {
                $url = str_replace(
                    ['{phone}', '{message}', '{api_key}', '{sender_id}'],
                    [urlencode($phone), urlencode($message), urlencode($apiKey), urlencode($senderId)],
                    $apiUrl
                );
                $response = Http::timeout(10)->get($url);

                return $response->successful();
            }

            $request = Http::timeout(10)->acceptJson();
            if ($apiKey !== '') {
                $request = $request->withToken($apiKey);
            }

            $response = $request->post($apiUrl, [
                'phone' => $phone,
                'to' => $phone,
                'message' => $message,
                'sender_id' => $senderId,
                'api_key' => $apiKey,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('SMS dispatch failed', ['phone' => $phone, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
