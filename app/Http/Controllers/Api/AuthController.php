<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\LoginRequest;
use App\Http\Requests\Student\RegisterRequest;
use App\Models\Plan;
use App\Models\Student;
use App\Services\OtpService;
use App\Services\Payment\PaymentGatewayResolver;
use App\Services\ReferralService;
use App\Services\SmsService;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends BaseApiController
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $student = DB::transaction(function () use ($request) {
            return Student::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'type' => $request->type,
                'trial_ends_at' => now()->addDays(7),
                'referral_code' => 'SIKHU-'.strtoupper(Str::random(5)),
                'referred_by_student_id' => $request->query('ref')
                    ? Student::where('referral_code', $request->query('ref'))->value('id')
                    : null,
            ]);
        });

        app(ReferralService::class)->recordRegistration($student, $request);
        $student->sendApiEmailVerificationNotification();

        $token = $student->createToken('sikhun_mobile_app')->plainTextToken;

        return $this->success(['student' => $student, 'token' => $token], 'Registered successfully', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $student = Student::where('email', $request->email)->first();

        if (! $student || ! Auth::guard('web')->getProvider()->validateCredentials($student, ['password' => $request->password])) {
            return $this->error('These credentials do not match our records.', [], 401);
        }

        if ($student->status === 'inactive') {
            return $this->error('Your account has been deactivated.', [], 403);
        }

        $token = $student->createToken('sikhun_mobile_app')->plainTextToken;

        return $this->success([
            'student' => $student,
            'token' => $token,
            'email_verified' => $student->hasVerifiedEmail(),
        ], 'Logged in successfully');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success($request->user());
    }

    public function sendVerification(Request $request): JsonResponse
    {
        /** @var Student $student */
        $student = $request->user('sanctum');

        if (! $student->hasVerifiedEmail()) {
            $student->sendApiEmailVerificationNotification();
        }

        return $this->success(null, $student->hasVerifiedEmail()
            ? 'Email is already verified.'
            : 'A verification link has been sent to your email.');
    }

    public function verifyEmail(Request $request, string $id, string $hash): JsonResponse
    {
        $student = Student::findOrFail($id);
        abort_unless(hash_equals($hash, sha1($student->getEmailForVerification())), 403);

        if (! $student->hasVerifiedEmail()) {
            $student->markEmailAsVerified();
        }

        return $this->success(['verified' => true], 'Email verified successfully.');
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email', 'max:255']]);
        $status = Password::broker('students')->sendResetLink($request->only('email'));

        return $this->success(null, $status === Password::RESET_THROTTLED
            ? 'Please wait before requesting another reset link.'
            : 'If an account exists, a password reset link has been sent.');
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::broker('students')->reset($data, function (Student $student, string $password): void {
            $student->forceFill([
                'password' => $password,
                'remember_token' => Str::random(60),
            ])->save();
        });

        if ($status !== Password::PASSWORD_RESET) {
            return $this->error('The reset token is invalid or expired.', [], 422);
        }

        return $this->success(null, 'Password reset successfully.');
    }

    public function config(SmsService $sms, PaymentGatewayResolver $resolver): JsonResponse
    {
        return $this->success([
            'sms_otp_enabled' => $sms->isOtpEnabled(),
            'payment_methods' => $resolver->enabledMethods(),
        ]);
    }

    public function sendOtp(Request $request, OtpService $otp): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'purpose' => ['nullable', 'in:login,register,verify'],
        ]);

        try {
            $result = $otp->send($data['phone'], $data['purpose'] ?? 'login', $request->ip());

            return $this->success($result, 'OTP sent successfully.');
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), [], 422);
        }
    }

    public function otpLogin(Request $request, OtpService $otp): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        if (! $otp->verify($data['phone'], $data['code'], 'login')) {
            return $this->error('Invalid or expired OTP code.', [], 422);
        }

        $normalized = $otp->normalizePhone($data['phone']);
        $student = Student::where('phone', $normalized)->first();

        if (! $student) {
            return $this->error('No account found with this phone number.', [], 404);
        }

        if ($student->status === 'inactive') {
            return $this->error('Your account has been deactivated.', [], 403);
        }

        if (! $student->phone_verified_at) {
            $student->update(['phone_verified_at' => now()]);
        }

        $token = $student->createToken('sikhun_mobile_app')->plainTextToken;

        return $this->success([
            'student' => $student,
            'token' => $token,
            'phone_verified' => true,
        ], 'Logged in successfully');
    }

    public function otpRegister(Request $request, OtpService $otp): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'code' => ['required', 'string', 'size:6'],
            'type' => ['required', 'in:ssc,hsc,admission,job,university'],
            'email' => ['nullable', 'email', 'max:255', 'unique:students,email'],
        ]);

        if (! $otp->verify($data['phone'], $data['code'], 'register')) {
            return $this->error('Invalid or expired OTP code.', [], 422);
        }

        $normalized = $otp->normalizePhone($data['phone']);
        if (Student::where('phone', $normalized)->exists()) {
            return $this->error('An account with this phone number already exists.', [], 422);
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
        $token = $student->createToken('sikhun_mobile_app')->plainTextToken;

        return $this->success(['student' => $student, 'token' => $token], 'Registered successfully', 201);
    }
}
