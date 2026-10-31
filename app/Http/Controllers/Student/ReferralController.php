<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ReferralEvent;
use App\Models\Student;
use Inertia\Inertia;
use Inertia\Response;

class ReferralController extends Controller
{
    public function index(): Response
    {
        /** @var Student $student */
        $student = auth('web')->user();
        $referrals = $student->referralsMade()
            ->with('referee:id,name,created_at')
            ->latest()
            ->get(['id', 'referrer_student_id', 'referee_student_id', 'status', 'fraud_status', 'registered_at', 'paid_at', 'referrer_reward', 'referee_reward', 'rewarded_at', 'created_at']);
        $events = ReferralEvent::where('referrer_student_id', $student->id)
            ->selectRaw('event_type, count(*) as aggregate')
            ->groupBy('event_type')
            ->pluck('aggregate', 'event_type');

        return Inertia::render('Student/Referral/Index', [
            'referralCode' => $student->referral_code,
            'referralLink' => url('/r/'.$student->referral_code),
            'stats' => [
                'total_referred' => $referrals->count(),
                'total_converted' => $referrals->where('status', 'rewarded')->count(),
                'total_earned' => (float) $referrals->where('status', 'rewarded')->sum('referrer_reward'),
                'clicks' => (int) ($events['click'] ?? 0),
                'registrations' => (int) ($events['registered'] ?? 0),
                'paid' => (int) ($events['paid'] ?? 0),
            ],
            'referrals' => $referrals,
        ]);
    }
}
