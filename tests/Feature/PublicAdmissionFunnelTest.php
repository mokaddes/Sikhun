<?php

namespace Tests\Feature;

use App\Models\AdmissionNotice;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicAdmissionFunnelTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_surfaces_admission_funnel_and_featured_admissions(): void
    {
        AdmissionNotice::create([
            'title' => 'ঢাকা বিশ্ববিদ্যালয় আন্ডারগ্র্যাজুয়েট ভর্তি ২০২৬-২০২৭',
            'slug' => 'du-admission-2026',
            'institution_name' => 'ঢাকা বিশ্ববিদ্যালয় (DU)',
            'description' => 'ঢাকা বিশ্ববিদ্যালয় স্নাতক ভর্তি নির্দেশিকা ও পরীক্ষার বিবরণ।',
            'university_type' => 'public',
            'application_deadline' => now()->addDays(14),
            'status' => 'published',
            'is_published' => true,
            'is_featured' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->has('featuredAdmissions')
        );
    }

    public function test_admission_notice_index_loads_successfully(): void
    {
        AdmissionNotice::create([
            'title' => 'রাজশাহী বিশ্ববিদ্যালয় স্নাতক ভর্তি ২০২৬',
            'slug' => 'ru-admission-2026',
            'institution_name' => 'রাজশাহী বিশ্ববিদ্যালয় (RU)',
            'description' => 'রাজশাহী বিশ্ববিদ্যালয় স্নাতক প্রথম বর্ষ ভর্তি বিজ্ঞপ্তি।',
            'university_type' => 'public',
            'application_deadline' => now()->addDays(20),
            'status' => 'published',
            'is_published' => true,
        ]);

        $response = $this->get('/admission-notice');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Admissions/Index')
            ->has('notices.data', 1)
        );
    }

    public function test_admission_notice_show_provides_practice_bridge_and_sample_questions(): void
    {
        $notice = AdmissionNotice::create([
            'title' => 'ঢাকা বিশ্ববিদ্যালয় স্নাতক ভর্তি ২০২৬-২০২৭',
            'slug' => 'du-undergraduate-2026',
            'institution_name' => 'ঢাকা বিশ্ববিদ্যালয় (DU)',
            'description' => 'ঢাকা বিশ্ববিদ্যালয় ইউনিটভিত্তিক বিস্তারিত ভর্তি বিজ্ঞপ্তি ও পরীক্ষার যোগ্যতা।',
            'university_type' => 'public',
            'application_deadline' => now()->addDays(15),
            'status' => 'published',
            'is_published' => true,
        ]);

        $subject = QuestionSubject::create([
            'name_bn' => 'বাংলা',
            'name_en' => 'Bangla',
            'slug' => 'bangla',
            'segment' => 'admission',
        ]);

        // Seed sample question
        QuestionBank::create([
            'subject_id' => $subject->id,
            'segment' => 'admission',
            'origin_tag' => 'DU-A',
            'question_text' => 'বাংলা বর্ণমালায় যৌগিক স্বরজ্ঞাপক বর্ণ কয়টি?',
            'question_type' => 'mcq',
            'options' => [
                ['key' => 'A', 'text' => '২টি'],
                ['key' => 'B', 'text' => '৩টি'],
                ['key' => 'C', 'text' => '৪টি'],
                ['key' => 'D', 'text' => '৫টি'],
            ],
            'correct_option' => 'A',
            'explanation' => 'যৌগিক স্বরজ্ঞাপক বর্ণ ২টি:  ঐ এবং ঔ।',
            'year' => 2024,
            'status' => 'published',
            'verification_status' => 'verified',
        ]);

        $response = $this->get("/admission-notice/{$notice->slug}");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Admissions/Show')
            ->has('notice')
            ->has('practiceBridge')
            ->has('sampleQuestions')
            ->where('practiceBridge.university_key', 'DU')
        );
    }
}
