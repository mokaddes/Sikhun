<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Student;
use App\Services\OtpService;
use App\Services\ReferralService;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpAuthController extends Controller
{
    public function send(Request $request, OtpService $otp): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'purpose' => ['nullable', 'in:login,register,verify'],
        ]);

        try {
            $result = $otp->send($data['phone'], $data['purpose'] ?? 'login', $request->ip());

            return response()->json([
                'ok' => true,
                'phone' => $result['phone'],
                'expires_in' => $result['expires_in'],
                'message' => 'OTP sent successfully.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function login(Request $request, OtpService $otp): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        if (! $otp->verify($data['phone'], $data['code'], 'login')) {
            throw ValidationException::withMessages([
                'code' => 'Invalid or expired OTP code.',
            ]);
        }

        $normalized = $otp->normalizePhone($data['phone']);
        $student = Student::where('phone', $normalized)->first();

        if (! $student) {
            throw ValidationException::withMessages([
                'phone' => 'No account found with this phone number. Please register first.',
            ]);
        }

        if ($student->status === 'inactive') {
            throw ValidationException::withMessages([
                'phone' => 'Your account has been deactivated. Please contact support.',
            ]);
        }

        if (! $student->phone_verified_at) {
            $student->update(['phone_verified_at' => now()]);
        }

        Auth::guard('web')->login($student, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function register(Request $request, OtpService $otp): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'code' => ['required', 'string', 'size:6'],
            'type' => ['required', 'in:ssc,hsc,admission,job,university'],
            'email' => ['nullable', 'email', 'max:255', 'unique:students,email'],
        ]);

        if (! $otp->verify($data['phone'], $data['code'], 'register')) {
            throw ValidationException::withMessages([
                'code' => 'Invalid or expired OTP code.',
            ]);
        }

        $normalized = $otp->normalizePhone($data['phone']);
        if (Student::where('phone', $normalized)->exists()) {
            throw ValidationException::withMessages([
                'phone' => 'An account with this phone number already exists.',
            ]);
        }

        $student = DB::transaction(function () use ($data, $normalized, $request) {
            $student = Student::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $normalized,
                'phone_verified_at' => now(),
                'password' => Str::random(24),
                'type' => $data['type'],
                'referral_code' => 'SIKHU-'.strtoupper(Str::random(5)),
                'referred_by_student_id' => $request->query('ref')
                    ? Student::where('referral_code', $request->query('ref'))->value('id')
                    : null,
            ]);

            $freePlan = Plan::where('is_free', true)->orWhere('price_monthly', 0)->first();
            if ($freePlan) {
                app(SubscriptionService::class)->assign($student, $freePlan, 12);
            }

            return $student;
        });

        app(ReferralService::class)->recordRegistration($student, $request);
        Auth::guard('web')->login($student, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('success', 'স্বাগতম! আপনার ফোন নম্বর যাচাই করে অ্যাকাউন্ট তৈরি হয়েছে।');
    }
}
