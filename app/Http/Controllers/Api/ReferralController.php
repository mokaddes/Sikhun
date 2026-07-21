<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class ReferralController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $student = auth('sanctum')->user();

        return $this->success($student->referralsMade()->with('referee:id,name,created_at')->latest()->get());
    }

    public function stats(): JsonResponse
    {
        $student = auth('sanctum')->user();
        $referrals = $student->referralsMade;

        return $this->success([
            'referral_code' => $student->referral_code,
            'referral_link' => url('/register?ref='.$student->referral_code),
            'total_referred' => $referrals->count(),
            'total_converted' => $referrals->where('status', 'rewarded')->count(),
            'total_earned' => (float) $referrals->where('status', 'rewarded')->sum('referrer_reward'),
        ]);
    }
}
