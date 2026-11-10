<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionNotice;
use App\Services\Admissions\AdmissionScraperService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdmissionNoticeController extends Controller
{
    public function __construct(
        private readonly AdmissionScraperService $scraperService
    ) {}

    public function index(Request $request): Response
    {
        $query = AdmissionNotice::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('type') && in_array($request->string('type'), ['public', 'guccho', 'private'], true)) {
            $query->where('university_type', $request->string('type'));
        }

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('institution_name', 'like', $term)
                    ->orWhere('slug', 'like', $term);
            });
        }

        return Inertia::render('Admin/Admissions/Index', [
            'notices' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['status', 'type', 'search']),
            'counts' => [
                'total' => AdmissionNotice::count(),
                'draft' => AdmissionNotice::where('status', 'draft')->count(),
                'published' => AdmissionNotice::where('status', 'published')->count(),
                'public' => AdmissionNotice::where('university_type', 'public')->count(),
                'guccho' => AdmissionNotice::where('university_type', 'guccho')->count(),
                'private' => AdmissionNotice::where('university_type', 'private')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Admissions/Form', [
            'notice' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'institution_name' => ['required', 'string', 'max:255'],
            'university_type' => ['required', 'in:public,guccho,private'],
            'academic_year' => ['required', 'string', 'max:50'],
            'units' => ['nullable', 'array'],
            'application_start' => ['nullable', 'date'],
            'application_start_text' => ['nullable', 'string', 'max:255'],
            'application_deadline' => ['nullable', 'date'],
            'application_deadline_text' => ['nullable', 'string', 'max:255'],
            'admit_card_date_text' => ['nullable', 'string', 'max:255'],
            'minimum_gpa_ssc' => ['nullable', 'string', 'max:50'],
            'minimum_gpa_hsc' => ['nullable', 'string', 'max:50'],
            'total_gpa_required' => ['nullable', 'string', 'max:100'],
            'application_fee' => ['nullable', 'string', 'max:100'],
            'apply_url' => ['nullable', 'url', 'max:1000'],
            'official_website_url' => ['nullable', 'url', 'max:1000'],
            'notice_image_url' => ['nullable', 'string', 'max:1000'],
            'featured_image_url' => ['nullable', 'string', 'max:1000'],
            'description' => ['required', 'string'],
            'eligibility_criteria' => ['nullable', 'string'],
            'marks_distribution' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published'],
            'is_featured' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ]);

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (AdmissionNotice::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }
        $validated['slug'] = $slug;

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $notice = AdmissionNotice::create($validated);

        return redirect()->route('admin.admissions.index')->with('success', 'ভর্তি বিজ্ঞপ্তি সফলভাবে তৈরি করা হয়েছে।');
    }

    public function edit(AdmissionNotice $admission): Response
    {
        return Inertia::render('Admin/Admissions/Form', [
            'notice' => $admission,
        ]);
    }

    public function update(Request $request, AdmissionNotice $admission): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'institution_name' => ['required', 'string', 'max:255'],
            'university_type' => ['required', 'in:public,guccho,private'],
            'academic_year' => ['required', 'string', 'max:50'],
            'units' => ['nullable', 'array'],
            'application_start' => ['nullable', 'date'],
            'application_start_text' => ['nullable', 'string', 'max:255'],
            'application_deadline' => ['nullable', 'date'],
            'application_deadline_text' => ['nullable', 'string', 'max:255'],
            'admit_card_date_text' => ['nullable', 'string', 'max:255'],
            'minimum_gpa_ssc' => ['nullable', 'string', 'max:50'],
            'minimum_gpa_hsc' => ['nullable', 'string', 'max:50'],
            'total_gpa_required' => ['nullable', 'string', 'max:100'],
            'application_fee' => ['nullable', 'string', 'max:100'],
            'apply_url' => ['nullable', 'url', 'max:1000'],
            'official_website_url' => ['nullable', 'url', 'max:1000'],
            'notice_image_url' => ['nullable', 'string', 'max:1000'],
            'featured_image_url' => ['nullable', 'string', 'max:1000'],
            'description' => ['required', 'string'],
            'eligibility_criteria' => ['nullable', 'string'],
            'marks_distribution' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published'],
            'is_featured' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ]);

        if ($validated['status'] === 'published' && ! $admission->published_at) {
            $validated['published_at'] = now();
        }

        $admission->update($validated);

        return redirect()->route('admin.admissions.index')->with('success', 'ভর্তি বিজ্ঞপ্তি সফলভাবে আপডেট করা হয়েছে।');
    }

    public function togglePublish(AdmissionNotice $admission): RedirectResponse
    {
        if ($admission->status === 'published') {
            $admission->update(['status' => 'draft']);

            return back()->with('success', 'বিজ্ঞপ্তি ড্রাফট হিসেবে সংরক্ষণ করা হয়েছে।');
        }

        $admission->update([
            'status' => 'published',
            'published_at' => $admission->published_at ?? now(),
        ]);

        return back()->with('success', 'বিজ্ঞপ্তি সফলভাবে ওয়েবসাইটে প্রকাশিত হয়েছে।');
    }

    public function destroy(AdmissionNotice $admission): RedirectResponse
    {
        $admission->delete();

        return back()->with('success', 'ভর্তি বিজ্ঞপ্তি মুছে ফেলা হয়েছে।');
    }

    public function scrapeNow(): RedirectResponse
    {
        $count = $this->scraperService->scrapeAll();

        return back()->with('success', "স্ক্র্যাপিং সম্পন্ন হয়েছে। {$count} টি নতুন/হালনাগাদ ভর্তি বিজ্ঞপ্তি সংগ্রহ করা হয়েছে।");
    }
}
