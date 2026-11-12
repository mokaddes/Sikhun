<?php

namespace Tests\Feature;

use App\Models\AdmissionNotice;
use App\Services\Admissions\AdmissionScraperService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdmissionScraperServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_verified_circulars_uses_first_or_create_and_preserves_customizations(): void
    {
        // 1. Pre-seed a customized record for RU
        $customRU = AdmissionNotice::create([
            'slug' => 'rajshahi-university-undergraduate-admission-circular-2026-2027',
            'title' => 'Custom Admin Title for RU Admission',
            'institution_name' => 'রাজশাহী বিশ্ববিদ্যালয় (RU)',
            'university_type' => 'public',
            'academic_year' => '2026-2027',
            'application_fee' => 'Custom 500 BDT',
            'apply_url' => 'https://admission.ru.ac.bd',
            'official_website_url' => 'https://ru.ac.bd',
            'description' => '<p>Custom Admin Description</p>',
            'status' => 'published',
        ]);

        $service = app(AdmissionScraperService::class);
        $imported = $service->seedVerifiedCirculars();

        // 2. Fetch the RU notice again; it should retain custom admin title and fee
        $ruNotice = AdmissionNotice::where('slug', 'rajshahi-university-undergraduate-admission-circular-2026-2027')->first();
        $this->assertNotNull($ruNotice);
        $this->assertSame('Custom Admin Title for RU Admission', $ruNotice->title);
        $this->assertSame('Custom 500 BDT', $ruNotice->application_fee);

        // Other foundational circulars should be seeded
        $duNotice = AdmissionNotice::where('slug', 'dhaka-university-undergraduate-admission-circular-2026-2027')->first();
        $this->assertNotNull($duNotice);
        $this->assertSame('https://admission.eis.du.ac.bd', $duNotice->apply_url);
    }

    public function test_official_domains_registry_covers_major_universities(): void
    {
        $service = app(AdmissionScraperService::class);

        $du = $service->resolveOfficialUrls('ঢাকা বিশ্ববিদ্যালয় (DU)');
        $this->assertSame('https://du.ac.bd', $du['official_website_url']);
        $this->assertSame('https://admission.eis.du.ac.bd', $du['apply_url']);

        $ru = $service->resolveOfficialUrls('রাজশাহী বিশ্ববিদ্যালয় (RU)');
        $this->assertSame('https://ru.ac.bd', $ru['official_website_url']);
        $this->assertSame('https://admission.ru.ac.bd', $ru['apply_url']);

        $buet = $service->resolveOfficialUrls('বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয় (BUET)');
        $this->assertSame('https://buet.ac.bd', $buet['official_website_url']);
        $this->assertStringContainsString('buet.ac.bd', $buet['apply_url']);

        $gst = $service->resolveOfficialUrls('জিএসটি গুচ্ছ বিশ্ববিদ্যালয় (GST Cluster)');
        $this->assertSame('https://gstadmission.ac.bd', $gst['official_website_url']);
        $this->assertSame('https://gstadmission.ac.bd', $gst['apply_url']);

        $medical = $service->resolveOfficialUrls('মেডিকেল ও ডেন্টাল কলেজ (MBBS/BDS)');
        $this->assertSame('https://dgme.gov.bd', $medical['official_website_url']);
        $this->assertSame('https://dgme.teletalk.com.bd', $medical['apply_url']);

        $cu = $service->resolveOfficialUrls('চট্টগ্রাম বিশ্ববিদ্যালয় (CU)');
        $this->assertSame('https://cu.ac.bd', $cu['official_website_url']);
        $this->assertSame('https://admission.cu.ac.bd', $cu['apply_url']);

        $ju = $service->resolveOfficialUrls('জাহাঙ্গীরনগর বিশ্ববিদ্যালয় (JU)');
        $this->assertSame('https://juniv.edu', $ju['official_website_url']);
        $this->assertSame('https://admission.juniv.edu', $ju['apply_url']);
    }

    public function test_third_party_aggregator_domains_are_never_used_as_official(): void
    {
        $this->assertTrue(AdmissionScraperService::isThirdPartyAggregator('https://students.bd/admission-notice'));
        $this->assertTrue(AdmissionScraperService::isThirdPartyAggregator('https://studyonlinebd.com/notice/123'));
        $this->assertTrue(AdmissionScraperService::isThirdPartyAggregator('https://eduresultbd.com/circular'));
        $this->assertTrue(AdmissionScraperService::isThirdPartyAggregator('https://facebook.com/circular'));
        $this->assertFalse(AdmissionScraperService::isThirdPartyAggregator('https://admission.eis.du.ac.bd'));
        $this->assertFalse(AdmissionScraperService::isThirdPartyAggregator('https://du.ac.bd'));

        $service = app(AdmissionScraperService::class);
        $urls = $service->resolveOfficialUrls('অজানা বিশ্ববিদ্যালয়', 'https://students.bd/post/123');
        $this->assertNull($urls['apply_url']);
        $this->assertNull($urls['official_website_url']);
    }

    public function test_generate_blog_content_uses_official_links(): void
    {
        $service = app(AdmissionScraperService::class);

        // With genuine official URL
        $html = $service->generateBlogContent('ঢাকা বিশ্ববিদ্যালয় (DU)', 'ঢাবি ভর্তি', 'বিবরণ', 'public', 'https://admission.eis.du.ac.bd');
        $this->assertStringContainsString('https://admission.eis.du.ac.bd', $html);
        $this->assertStringContainsString('অফিসিয়াল ভর্তি ওয়েবসাইট', $html);

        // With 3rd party aggregator URL (should be sanitized away)
        $htmlAggregator = $service->generateBlogContent('ঢাকা বিশ্ববিদ্যালয় (DU)', 'ঢাবি ভর্তি', 'বিবরণ', 'public', 'https://students.bd/notice');
        $this->assertStringNotContainsString('students.bd', $htmlAggregator);
        $this->assertStringContainsString('অফিসিয়াল ভর্তি ওয়েবসাইট', $htmlAggregator);
    }
}
