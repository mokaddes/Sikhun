<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\ReferralEvent;
use App\Services\ReferralService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            'funnel' => ReferralEvent::query()
                ->selectRaw('event_type, count(*) as aggregate')
                ->groupBy('event_type')
                ->pluck('aggregate', 'event_type'),
        ]);
    }

    public function review(Request $request, Referral $referral, ReferralService $service): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
        ]);

        $service->review($referral, $request->user('admin'), $validated['decision']);

        return back()->with('success', 'Referral review saved.');
    }
}
