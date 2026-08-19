<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FreeCampaignRequest;
use App\Models\FreeCampaign;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FreeCampaignController extends Controller
{
    public function index(Request $request): Response
    {
        $campaigns = FreeCampaign::query()
            ->withCount('students')
            ->when($request->status, fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/FreeCampaigns/Index', [
            'campaigns' => $campaigns,
            'filters' => $request->only('status'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/FreeCampaigns/Form', [
            'students' => Student::where('status', 'active')->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function store(FreeCampaignRequest $request): RedirectResponse
    {
        $campaign = FreeCampaign::create($request->safe()->except('student_ids'));

        if ($request->input('scope') === 'selected') {
            $campaign->students()->sync($request->input('student_ids', []));
        }

        return redirect()->route('admin.free-campaigns.index')->with('success', 'Free campaign created.');
    }

    public function edit(FreeCampaign $freeCampaign): Response
    {
        return Inertia::render('Admin/FreeCampaigns/Form', [
            'campaign' => $freeCampaign->load('students:id'),
            'students' => Student::where('status', 'active')->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function update(FreeCampaignRequest $request, FreeCampaign $freeCampaign): RedirectResponse
    {
        $freeCampaign->update($request->safe()->except('student_ids'));

        if ($request->input('scope') === 'selected') {
            $freeCampaign->students()->sync($request->input('student_ids', []));
        } else {
            $freeCampaign->students()->sync([]);
        }

        return redirect()->route('admin.free-campaigns.index')->with('success', 'Free campaign updated.');
    }

    public function destroy(FreeCampaign $freeCampaign): RedirectResponse
    {
        $freeCampaign->delete();

        return back()->with('success', 'Free campaign deleted.');
    }
}
