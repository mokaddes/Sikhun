<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\LoginRequest;
use App\Http\Requests\Student\RegisterRequest;
use App\Models\Student;
use App\Services\ReferralService;
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
}
