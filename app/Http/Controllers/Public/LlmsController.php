<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdmissionNotice;
use App\Models\BlogPost;
use App\Models\Book;
use App\Models\Course;
use App\Models\JobCircular;
use App\Models\QuestionBank;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * Dynamic, cached /llms.txt endpoint for Generative Engine Optimization (GEO).
 *
 * Exposes Sikhun.com's authoritative Bengali + English educational catalog,
 * live platform counts, top courses, digital books, university admission
 * circulars, job circulars, and question bank coverage to AI crawlers
 * (GPTBot, OAI-SearchBot, PerplexityBot, ClaudeBot, Google-Extended).
 */
class LlmsController extends Controller
{
    public function index(): Response
    {
        $content = Cache::remember('llms_txt_payload_v1', 3600, function () {
            return $this->buildLlmsText();
        });

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function buildLlmsText(): string
    {
        $baseUrl = rtrim(config('app.url', 'https://sikhun.com'), '/');

        $booksCount = Book::published()->count();
        $coursesCount = Course::active()->count();
        $questionsCount = QuestionBank::count();
        $admissionsCount = AdmissionNotice::published()->count();
        $jobsCount = JobCircular::where('title', 'not like', '%admission%')
            ->where('title', 'not like', '%ভর্তি%')
            ->count();

        $topCourses = Course::active()
            ->select(['title', 'slug', 'price', 'level'])
            ->latest()
            ->take(10)
            ->get();

        $topBooks = Book::published()
            ->select(['title', 'slug', 'subject', 'level', 'is_free'])
            ->latest()
            ->take(15)
            ->get();

        $latestAdmissions = AdmissionNotice::published()
            ->select(['title', 'slug', 'institution_name', 'application_deadline'])
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->take(12)
            ->get();

        $latestJobs = JobCircular::select(['title', 'slug', 'organization', 'deadline'])
            ->where('title', 'not like', '%admission%')
            ->where('title', 'not like', '%ভর্তি%')
            ->orderByRaw('CASE WHEN deadline >= CURRENT_DATE THEN 0 ELSE 1 END ASC')
            ->latest('updated_at')
            ->take(12)
            ->get();

        $latestBlogs = BlogPost::published()
            ->select(['title', 'slug'])
            ->orderByDesc('published_at')
            ->take(10)
            ->get();

        $lines = [
            '# Sikhun.com (শিখুন)',
            '',
            "> Sikhun.com is Bangladesh's premier AI-powered digital learning and exam preparation platform, empowering students across HSC, SSC, University Admission (DU, RU, BUET, GST Cluster, Medical/Dental), and Competitive Job Examinations (BCS, Bank, Primary Teacher).",
            '',
            '## Platform Snapshot (Live)',
            '',
            "- **Digital Academic Books:** {$booksCount} published textbooks with browser reader, chapter-aware AI Book Tutor (RAG), and flashcard generation.",
            "- **Mentor-Led Online Courses:** {$coursesCount} active structured courses.",
            "- **Question Bank:** {$questionsCount}+ curated past admission, board, and job exam MCQs with step-by-step AI explanations and Mistake Review (\"আমার ভুলসমূহ\").",
            "- **University Admission Circulars:** {$admissionsCount} verified active undergraduate admission notices.",
            "- **Job Circulars:** {$jobsCount}+ Government, Bank, NGO, and Private job circulars.",
            '- **Languages:** Bengali (`bn-BD`, primary) and English (`en-US`).',
            '',
            '## Key Public Hubs',
            '',
            "- [Home]({$baseUrl}/): AI-powered learning ecosystem overview, live stats, and featured content.",
            "- [Digital Library]({$baseUrl}/library): HSC, SSC, Admission, and Job textbooks with embedded AI study companion.",
            "- [Online Courses]({$baseUrl}/courses): Mentor-led live and recorded courses with certificates.",
            "- [University Admission Notices]({$baseUrl}/admission-notice): Official admission circulars, unit eligibility, deadlines, and past question banks for DU, RU, BUET, GST, Medical, CU, and JU.",
            "- [Job News Portal]({$baseUrl}/job-news): Latest Bangladesh government, bank, NGO, and private job circulars.",
            "- [Educational Blog]({$baseUrl}/blog): Bengali & English articles on Science, AI, Technology, and Academic Career.",
            "- [Subscription Plans]({$baseUrl}/plans): Free and affordable AI Study Plans (HSC, Admission Pass, BCS & Bank Pass).",
            "- [Student Tools Hub]({$baseUrl}/tools): GPA calculator, age calculator, and image resizer for admission/job applications.",
            "- [Board & Admission Result Checker]({$baseUrl}/results): Quick SSC, HSC, and university result lookup guide.",
            "- [About Sikhun]({$baseUrl}/p/about): Platform mission, pedagogy, and AI architecture.",
            "- [FAQ]({$baseUrl}/p/faq): Frequently asked questions about digital books, AI tutoring, exams, and subscriptions.",
            "- [Contact]({$baseUrl}/contact): Student support and mentor inquiries.",
            '',
        ];

        if ($topCourses->isNotEmpty()) {
            $lines[] = '## Featured Online Courses';
            $lines[] = '';
            foreach ($topCourses as $course) {
                $price = (float) $course->price === 0.0 ? 'Free' : 'BDT '.number_format((float) $course->price, 0);
                $lines[] = "- [{$course->title}]({$baseUrl}/courses/{$course->slug}) ({$price})";
            }
            $lines[] = '';
        }

        if ($topBooks->isNotEmpty()) {
            $lines[] = '## Featured Digital Library Books';
            $lines[] = '';
            foreach ($topBooks as $book) {
                $meta = implode(' · ', array_filter([
                    $book->level ? strtoupper((string) $book->level) : null,
                    $book->subject,
                    $book->is_free ? 'Free' : null,
                ]));
                $suffix = $meta ? " ({$meta})" : '';
                $lines[] = "- [{$book->title}]({$baseUrl}/library/{$book->slug}){$suffix}";
            }
            $lines[] = '';
        }

        if ($latestAdmissions->isNotEmpty()) {
            $lines[] = '## Active University Admission Circulars';
            $lines[] = '';
            foreach ($latestAdmissions as $notice) {
                $deadline = $notice->application_deadline ? ' — Deadline: '.$notice->application_deadline->toDateString() : '';
                $lines[] = "- [{$notice->institution_name}: {$notice->title}]({$baseUrl}/admission-notice/{$notice->slug}){$deadline}";
            }
            $lines[] = '';
        }

        if ($latestJobs->isNotEmpty()) {
            $lines[] = '## Latest Job Circulars (Bangladesh)';
            $lines[] = '';
            foreach ($latestJobs as $job) {
                $org = $job->organization ? " ({$job->organization})" : '';
                $lines[] = "- [{$job->title}]({$baseUrl}/job-news/{$job->slug}){$org}";
            }
            $lines[] = '';
        }

        if ($latestBlogs->isNotEmpty()) {
            $lines[] = '## Recent Educational Articles';
            $lines[] = '';
            foreach ($latestBlogs as $post) {
                $lines[] = "- [{$post->title}]({$baseUrl}/blog/{$post->slug})";
            }
            $lines[] = '';
        }

        $lines = array_merge($lines, [
            '## Entity & Citation Metadata',
            '',
            '- **Organization:** Sikhun.com (শিখুন)',
            "- **Website:** {$baseUrl}",
            "- **XML Sitemap:** {$baseUrl}/sitemap.xml",
            '- **Social:** https://www.facebook.com/sikhunbd',
            '- **Support Email:** support@sikhun.com',
            '- **Target Region:** Bangladesh (BD)',
            '- **Languages:** Bengali (bn-BD), English (en-US)',
            '',
        ]);

        return implode("\n", $lines);
    }
}
