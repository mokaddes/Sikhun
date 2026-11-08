<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionSource;
use App\Services\Questions\QuestionSourceManagerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuestionSourceController extends Controller
{
    public function __construct(
        protected QuestionSourceManagerService $sourceManagerService
    ) {}

    /**
     * Display Source Registry & Permission Management interface.
     */
    public function index(Request $request): Response
    {
        $query = QuestionSource::query()->withCount('questions');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%")
                  ->orWhere('base_url', 'like', "%{$search}%");
            });
        }

        if ($request->filled('source_type') && $request->input('source_type') !== 'all') {
            $query->where('source_type', $request->input('source_type'));
        }

        if ($request->filled('content_category') && $request->input('content_category') !== 'all') {
            $query->byCategory($request->input('content_category'));
        }

        if ($request->filled('permission_status') && $request->input('permission_status') !== 'all') {
            $query->where('permission_status', $request->input('permission_status'));
        }

        if ($request->filled('robots_txt_status') && $request->input('robots_txt_status') !== 'all') {
            $query->where('robots_txt_status', $request->input('robots_txt_status'));
        }

        if ($request->has('is_active') && $request->input('is_active') !== 'all' && $request->input('is_active') !== null) {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $sources = $query->orderBy('name')->paginate(15)->withQueryString();

        $stats = [
            'total' => QuestionSource::count(),
            'active' => QuestionSource::where('is_active', true)->count(),
            'official' => QuestionSource::whereIn('source_type', ['official_university', 'official_commission'])->count(),
            'archives' => QuestionSource::where('source_type', 'educational_archive')->count(),
            'authorized' => QuestionSource::whereIn('permission_status', ['authorized', 'public_domain', 'fair_use_educational'])->count(),
            'robots_allowed' => QuestionSource::where('robots_txt_status', 'allowed')->count(),
        ];

        return Inertia::render('Admin/Questions/Sources/Index', [
            'sources' => $sources,
            'stats' => $stats,
            'filters' => $request->only(['search', 'source_type', 'content_category', 'permission_status', 'robots_txt_status', 'is_active']),
        ]);
    }

    /**
     * Store a newly created source.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'base_url' => ['required', 'url', 'max:500'],
            'source_type' => ['required', 'string', 'in:official_university,official_commission,educational_archive,public_portal,community'],
            'institution' => ['nullable', 'string', 'max:255'],
            'content_category' => ['required', 'string', 'in:admission,job,both'],
            'crawl_frequency' => ['required', 'string', 'in:hourly,daily,weekly,monthly,manual'],
            'robots_txt_status' => ['required', 'string', 'in:allowed,disallowed,conditional,unchecked'],
            'tos_review' => ['required', 'string', 'in:pending,compliant,restricted,violation_risk'],
            'tos_notes' => ['nullable', 'string'],
            'permission_status' => ['required', 'string', 'in:pending_review,authorized,public_domain,fair_use_educational,rejected'],
            'permission_document_url' => ['nullable', 'url', 'max:500'],
            'license_info' => ['required', 'string', 'max:255'],
            'commercial_redistribution' => ['boolean'],
            'attribution_requirements' => ['nullable', 'string'],
            'license_expiry' => ['nullable', 'date'],
            'rate_limit_rpm' => ['required', 'integer', 'min:1', 'max:300'],
            'crawl_delay_ms' => ['required', 'integer', 'min:500', 'max:30000'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        QuestionSource::create($validated);

        return redirect()->back()->with('success', 'নতুন প্রশ্ন সোর্স ও পারমিশন প্রোফাইল সফলভাবে যুক্ত করা হয়েছে।');
    }

    /**
     * Update an existing source.
     */
    public function update(Request $request, QuestionSource $questionSource): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'base_url' => ['required', 'url', 'max:500'],
            'source_type' => ['required', 'string', 'in:official_university,official_commission,educational_archive,public_portal,community'],
            'institution' => ['nullable', 'string', 'max:255'],
            'content_category' => ['required', 'string', 'in:admission,job,both'],
            'crawl_frequency' => ['required', 'string', 'in:hourly,daily,weekly,monthly,manual'],
            'robots_txt_status' => ['required', 'string', 'in:allowed,disallowed,conditional,unchecked'],
            'tos_review' => ['required', 'string', 'in:pending,compliant,restricted,violation_risk'],
            'tos_notes' => ['nullable', 'string'],
            'permission_status' => ['required', 'string', 'in:pending_review,authorized,public_domain,fair_use_educational,rejected'],
            'permission_document_url' => ['nullable', 'url', 'max:500'],
            'license_info' => ['required', 'string', 'max:255'],
            'commercial_redistribution' => ['boolean'],
            'attribution_requirements' => ['nullable', 'string'],
            'license_expiry' => ['nullable', 'date'],
            'rate_limit_rpm' => ['required', 'integer', 'min:1', 'max:300'],
            'crawl_delay_ms' => ['required', 'integer', 'min:500', 'max:30000'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $questionSource->update($validated);

        return redirect()->back()->with('success', 'প্রশ্ন সোর্স এবং লাইসেন্স সেটিংস সফলভাবে আপডেট করা হয়েছে।');
    }

    /**
     * Delete a source.
     */
    public function destroy(QuestionSource $questionSource): RedirectResponse
    {
        $name = $questionSource->name;
        $questionSource->delete();

        return redirect()->back()->with('success', "\"{$name}\" সোর্সটি সফলভাবে মুছে ফেলা হয়েছে।");
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(QuestionSource $questionSource): RedirectResponse
    {
        $questionSource->update(['is_active' => !$questionSource->is_active]);

        $statusMsg = $questionSource->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়';
        return redirect()->back()->with('success', "সোর্সটি সফলভাবে {$statusMsg} করা হয়েছে।");
    }

    /**
     * Check Robots.txt live.
     */
    public function checkRobots(QuestionSource $questionSource): RedirectResponse
    {
        $result = $this->sourceManagerService->checkRobotsTxt($questionSource);

        if ($result['success']) {
            return redirect()->back()->with('success', "Robots.txt পরীক্ষা সম্পন্ন: স্ট্যাটাস '{$result['status']}'। {$result['message']}");
        }

        return redirect()->back()->with('error', "Robots.txt পরীক্ষা ব্যর্থ হয়েছে: {$result['message']}");
    }

    /**
     * Test live connection to source.
     */
    public function testConnection(QuestionSource $questionSource): RedirectResponse
    {
        $result = $this->sourceManagerService->testConnection($questionSource);

        if ($result['success']) {
            return redirect()->back()->with('success', "সোর্স সফলভাবে কানেক্ট হয়েছে ({$result['response_time_ms']}ms, HTTP {$result['http_code']})।");
        }

        return redirect()->back()->with('error', "সোর্সে কানেক্ট করা সম্ভব হয়নি: {$result['message']}");
    }
}
