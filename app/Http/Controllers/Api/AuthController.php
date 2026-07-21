<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\LoginRequest;
use App\Http\Requests\Student\RegisterRequest;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
                'referral_code' => 'SIKHU-'.strtoupper(Str::random(5)),
                'referred_by_student_id' => $request->query('ref')
                    ? Student::where('referral_code', $request->query('ref'))->value('id')
                    : null,
            ]);
        });

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

        return $this->success(['student' => $student, 'token' => $token], 'Logged in successfully');
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
}
