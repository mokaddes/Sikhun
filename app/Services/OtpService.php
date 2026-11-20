<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    public const TTL_MINUTES = 5;
    public const COOLDOWN_SECONDS = 60;
    public const MAX_ATTEMPTS = 5;

    public function __construct(protected SmsService $sms) {}

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', trim($phone)) ?? '';
        if (str_starts_with($digits, '8801') && strlen($digits) === 13) {
            return substr($digits, 2);
        }
        if (str_starts_with($digits, '1') && strlen($digits) === 10) {
            return '0'.$digits;
        }

        return $digits;
    }

    public function send(string $phone, string $purpose = 'login', ?string $ip = null): array
    {
        if (! $this->sms->isOtpEnabled()) {
            throw new \RuntimeException('SMS OTP is currently disabled.');
        }

        $normalized = $this->normalizePhone($phone);
        if (! preg_match('/^01[3-9]\d{8}$/', $normalized)) {
            throw new \RuntimeException('Please enter a valid Bangladesh mobile number (e.g. 017XXXXXXXX).');
        }

        $recent = OtpCode::where('phone', $normalized)
            ->where('purpose', $purpose)
            ->latest('id')
            ->first();

        if ($recent && $recent->created_at->diffInSeconds(now()) < self::COOLDOWN_SECONDS) {
            $wait = self::COOLDOWN_SECONDS - $recent->created_at->diffInSeconds(now());
            throw new \RuntimeException("Please wait {$wait} seconds before requesting another OTP.");
        }

        $code = (string) random_int(100000, 999999);

        OtpCode::create([
            'phone' => $normalized,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::TTL_MINUTES),
            'ip_address' => $ip,
        ]);

        $sent = $this->sms->send($normalized, "Your Sikhun.com verification code is {$code}. Valid for 5 minutes.");
        if (! $sent) {
            throw new \RuntimeException('Failed to send OTP SMS. Please check SMS API configuration.');
        }

        return [
            'phone' => $normalized,
            'expires_in' => self::TTL_MINUTES * 60,
        ];
    }

    public function verify(string $phone, string $code, string $purpose = 'login'): bool
    {
        $normalized = $this->normalizePhone($phone);

        $record = OtpCode::where('phone', $normalized)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if (! $record) {
            return false;
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            return false;
        }

        $record->increment('attempts');

        if (! Hash::check(trim($code), $record->code_hash)) {
            return false;
        }

        $record->update(['verified_at' => now()]);

        return true;
    }
}
