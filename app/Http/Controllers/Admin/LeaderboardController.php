<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaderboardEntry;
use App\Services\LeaderboardService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LeaderboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Leaderboard/Index', [
            'entries' => LeaderboardEntry::with('student:id,name,email')->latest()->paginate(25),
        ]);
    }

    public function destroy(LeaderboardEntry $entry, LeaderboardService $leaderboard): RedirectResponse
    {
        $entry->delete();
        $leaderboard->invalidateAll();

        return back()->with('success', 'Leaderboard entry removed.');
    }
}
