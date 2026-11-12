<?php

namespace Tests\Feature;

use App\Http\Controllers\Public\SitemapController;
use App\Models\AdmissionNotice;
use App\Models\Book;
use App\Models\Course;
use App\Models\Student;
use App\Services\SiteSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SitemapController::clearCache();
    }

    public function test_robots_txt_contains_required_directives_and_bot_policies(): void
    {
        $path = public_path('robots.txt');
        $this->assertFileExists($path);

        $content = file_get_contents($path);
        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('User-agent: OAI-SearchBot', $content);
        $this->assertStringContainsString('User-agent: GPTBot', $content);
        $this->assertStringContainsString('Disallow: /admin/', $content);
        $this->assertStringContainsString('Disallow: /dashboard/', $content);
        $this->assertStringContainsString('Disallow: /mistakes/', $content);
        $this->assertStringContainsString('Disallow: /question-bank/', $content);
        $this->assertStringContainsString('Sitemap: https://sikhun.com/sitemap.xml', $content);
    }

    public function test_dynamic_sitemap_returns_valid_xml_with_correct_content_type(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'Sitemap output must be well-formed XML');
        $this->assertEquals('urlset', $xml->getName());
    }

    public function test_sitemap_contains_only_published_public_urls(): void
    {
        $publishedBook = Book::create([
            'title' => 'SEO Valid Book',
            'slug' => 'seo-valid-book',
            'price' => 100,
            'is_published' => true,
        ]);

        $draftBook = Book::create([
            'title' => 'Unpublished Draft Book',
            'slug' => 'unpublished-draft-book',
            'price' => 100,
            'is_published' => false,
        ]);

        $publishedAdmission = AdmissionNotice::create([
            'title' => 'DU Undergraduate Circular 2026',
            'slug' => 'du-undergraduate-circular-2026',
            'institution_name' => 'ঢাকা বিশ্ববিদ্যালয়',
            'university_type' => 'public',
            'academic_year' => '2026-2027',
            'description' => 'ঢাকা বিশ্ববিদ্যালয় ২০২৬ সার্কুলার।',
            'status' => 'published',
        ]);

        $draftAdmission = AdmissionNotice::create([
            'title' => 'RU Draft Circular',
            'slug' => 'ru-draft-circular',
            'institution_name' => 'রাজশাহী বিশ্ববিদ্যালয়',
            'university_type' => 'public',
            'academic_year' => '2026-2027',
            'description' => 'রাবি ড্রাফট সার্কুলার।',
            'status' => 'draft',
        ]);

        SitemapController::clearCache();

        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertStringContainsString('/library/seo-valid-book', $content);
        $this->assertStringNotContainsString('/library/unpublished-draft-book', $content);

        $this->assertStringContainsString('/admission-notice/du-undergraduate-circular-2026', $content);
        $this->assertStringNotContainsString('/admission-notice/ru-draft-circular', $content);

        // Private routes must never appear in sitemap
        $this->assertStringNotContainsString('/admin', $content);
        $this->assertStringNotContainsString('/dashboard', $content);
        $this->assertStringNotContainsString('/mistakes', $content);
    }

    public function test_homepage_has_unique_title_description_and_canonical(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();
        $baseUrl = rtrim(config('app.url'), '/');

        $this->assertStringContainsString('বাংলাদেশের প্রথম AI-চালিত শিক্ষা প্ল্যাটফর্ম', $content);
        $this->assertStringContainsString('ডিজিটালি বই পড়ুন, AI-এর সাথে চ্যাট করুন', $content);
        $this->assertStringContainsString('<link rel="canonical" href="'.$baseUrl.'/">', $content);
        $this->assertStringContainsString('<meta name="robots" content="index, follow">', $content);
    }

    public function test_homepage_has_open_graph_and_twitter_metadata(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();
        $baseUrl = rtrim(config('app.url'), '/');

        $this->assertStringContainsString('property="og:site_name"', $content);
        $this->assertStringContainsString('<meta property="og:type" content="website">', $content);
        $this->assertStringContainsString('<meta property="og:url" content="'.$baseUrl.'/">', $content);
        $this->assertStringContainsString('<meta name="twitter:card" content="summary_large_image">', $content);
        $this->assertStringContainsString('<meta name="twitter:site" content="@sikhun_com">', $content);
        $this->assertStringContainsString('hreflang="bn"', $content);
        $this->assertStringContainsString('hreflang="en"', $content);
    }

    public function test_homepage_has_valid_json_ld_schema(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();
        $this->assertStringContainsString('<script type="application/ld+json">', $content);

        // Extract JSON-LD payload
        preg_match('/<script type="application\/ld\+json">\s*(\{.*?\})\s*<\/script>/s', $content, $matches);
        $this->assertNotEmpty($matches, 'JSON-LD script tag must exist in raw HTML head');

        $json = json_decode($matches[1], true);
        $this->assertIsArray($json, 'JSON-LD payload must be valid JSON');
        $this->assertArrayHasKey('@graph', $json);

        $types = array_column($json['@graph'], '@type');
        $this->assertContains('WebSite', $types);
        $this->assertContains('EducationalOrganization', $types);
    }

    public function test_admission_notice_has_rich_seo_and_schema(): void
    {
        $notice = AdmissionNotice::create([
            'title' => 'BUET Undergraduate Admission 2026',
            'slug' => 'buet-undergraduate-admission-2026',
            'institution_name' => 'বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয় (BUET)',
            'university_type' => 'public',
            'academic_year' => '2026-2027',
            'description' => 'বুয়েট ২০২৬-২০২৭ শিক্ষাবর্ষে স্নাতক ভর্তি বিজ্ঞপ্তি।',
            'status' => 'published',
            'apply_url' => 'https://www.buet.ac.bd/web/#/admission',
        ]);

        $response = $this->get('/admission-notice/'.$notice->slug);
        $response->assertStatus(200);

        $content = $response->getContent();
        $baseUrl = rtrim(config('app.url'), '/');

        $this->assertStringContainsString('বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয় (BUET)', $content);
        $this->assertStringContainsString('<link rel="canonical" href="'.$baseUrl.'/admission-notice/buet-undergraduate-admission-2026">', $content);
        $this->assertStringContainsString('<meta name="robots" content="index, follow">', $content);

        // Verify JSON-LD Schema
        $this->assertStringContainsString('SpecialAnnouncement', $content);
        $this->assertStringContainsString('https://www.buet.ac.bd/web/#/admission', $content);
    }

    public function test_private_dashboard_pages_render_noindex_nofollow(): void
    {
        $student = Student::create([
            'name' => 'SEO Test Student',
            'email' => 'seotest-'.uniqid().'@sikhun.test',
            'password' => bcrypt('password'),
            'status' => 'active',
            'type' => 'admission',
            'referral_code' => 'SEOTEST'.strtoupper(uniqid()),
        ]);

        $response = $this->actingAs($student, 'web')->get('/dashboard');
        $response->assertStatus(200);

        $content = $response->getContent();
        $this->assertStringContainsString('<meta name="robots" content="noindex, nofollow">', $content);
    }

    public function test_missing_pages_return_404(): void
    {
        $response = $this->get('/this-page-definitely-does-not-exist-seo-404');
        $response->assertStatus(404);
    }

    public function test_legacy_routes_redirect_with_301(): void
    {
        $response = $this->get('/jobs');
        $response->assertStatus(301);
        $response->assertRedirect('/job-news');

        $responseCalc = $this->get('/calculators');
        $responseCalc->assertStatus(301);
        $responseCalc->assertRedirect('/tools');
    }

    public function test_seo_metadata_changes_correctly_between_pages(): void
    {
        $resHome = $this->get('/');
        $resLibrary = $this->get('/library');
        $resAdmissions = $this->get('/admission-notice');
        $resContact = $this->get('/contact');

        $this->assertStringContainsString('ডিজিটাল লাইব্রেরি', $resLibrary->getContent());
        $this->assertStringContainsString('ভর্তি বিজ্ঞপ্তি', $resAdmissions->getContent());
        $this->assertStringContainsString('যোগাযোগ করুন', $resContact->getContent());

        $this->assertNotEquals(
            $resHome->getContent(),
            $resLibrary->getContent()
        );
    }

    public function test_llms_txt_is_accessible_and_well_structured(): void
    {
        $path = public_path('llms.txt');
        $this->assertFileExists($path);

        $content = file_get_contents($path);
        $this->assertStringContainsString('# Sikhun.com', $content);
        $this->assertStringContainsString('https://sikhun.com/library', $content);
        $this->assertStringContainsString('https://sikhun.com/admission-notice', $content);
        $this->assertStringContainsString('https://sikhun.com/sitemap.xml', $content);
    }
}
