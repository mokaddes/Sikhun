<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ReferralController extends Controller
{
    public function index(): Response
    {
        $student = auth('web')->user();
        $referrals = $student->referralsMade()->with('referee:id,name,created_at')->latest()->get();

        return Inertia::render('Student/Referral/Index', [
            'referralCode' => $student->referral_code,
            'referralLink' => url('/register?ref='.$student->referral_code),
            'stats' => [
                'total_referred' => $referrals->count(),
                'total_converted' => $referrals->where('status', 'rewarded')->count(),
                'total_earned' => (float) $referrals->where('status', 'rewarded')->sum('referrer_reward'),
            ],
            'referrals' => $referrals,
        ]);
    }
}
