<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use Inertia\Inertia;
use Inertia\Response;

class ReferralController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Referrals/Index', [
            'referrals' => Referral::with(['referrer:id,name,email', 'referee:id,name,email'])
                ->latest()
                ->paginate(25),
        ]);
    }
}
