<?php

namespace App\Http\Controllers\Api;

use App\Models\ReferralEvent;
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
        $events = ReferralEvent::where('referrer_student_id', $student->id)
            ->selectRaw('event_type, count(*) as aggregate')
            ->groupBy('event_type')
            ->pluck('aggregate', 'event_type');

        return $this->success([
            'referral_code' => $student->referral_code,
            'referral_link' => url('/r/'.$student->referral_code),
            'total_referred' => $referrals->count(),
            'total_converted' => $referrals->where('status', 'rewarded')->count(),
            'total_earned' => (float) $referrals->where('status', 'rewarded')->sum('referrer_reward'),
                        'clicks' => (int) ($events['click'] ?? 0),
                        'registrations' => (int) ($events['registered'] ?? 0),
                        'paid' => (int) ($events['paid'] ?? 0),
        ]);
    }
}
