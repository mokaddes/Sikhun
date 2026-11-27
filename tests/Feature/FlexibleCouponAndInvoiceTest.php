<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AdmissionNotice;
use App\Models\Book;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Student;
use App\Services\Admissions\AdmissionScraperService;
use App\Services\InvoiceService;
use App\Services\PurchaseService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FlexibleCouponAndInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(array $overrides = []): Student
    {
        return Student::create(array_merge([
            'name' => 'Test Learner',
            'email' => 'learner-' . uniqid() . '@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'status' => 'active',
            'referral_code' => 'REF' . strtoupper(uniqid()),
        ], $overrides));
    }

    private function makeAdmin(): Admin
    {
        return Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin-' . uniqid() . '@sikhun.test',
            'password' => 'password',
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    private function makeBook(float $price = 300, string $title = 'Physics Guide'): Book
    {
        return Book::create([
            'title' => $title,
            'slug' => 'book-' . uniqid(),
            'price' => $price,
            'is_free' => false,
            'is_published' => true,
            'total_pages' => 120,
        ]);
    }

    private function makeCourse(float $price = 500, string $title = 'HSC Physics Masterclass'): Course
    {
        return Course::create([
            'title' => $title,
            'slug' => 'course-' . uniqid(),
            'description' => 'Course desc',
            'level' => 'hsc',
            'price' => $price,
            'is_active' => true,
        ]);
    }

    private function makePlan(float $priceMonthly = 299): Plan
    {
        return Plan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro-' . uniqid(),
            'price_monthly' => $priceMonthly,
            'ai_chat_minutes' => 120,
            'ai_vision_limit' => 30,
            'upload_book_limit' => 5,
            'monthly_reupload_limit' => 5,
            'ai_exam_count' => 30,
            'trial_ai_minutes' => 10,
            'is_active' => true,
        ]);
    }

    public function test_admission_merge_duplicates_redirects_old_slug_and_scraper_dedups(): void
    {
        $duPrimary = AdmissionNotice::create([
            'title' => 'ঢাকা বিশ্ববিদ্যালয় ভর্তি সার্কুলার ২০২৬-২০২৭',
            'slug' => 'dhaka-university-circular-main',
            'institution_name' => 'ঢাকা বিশ্ববিদ্যালয় (DU)',
            'university_type' => 'public',
            'academic_year' => '2026-2027',
            'apply_url' => 'https://admission.eis.du.ac.bd',
            'official_website_url' => 'https://du.ac.bd',
            'description' => '<p>Main circular body with complete details.</p>',
            'status' => 'published',
            'views_count' => 50,
            'published_at' => now()->subDays(2),
        ]);

        $duDraft = AdmissionNotice::create([
            'title' => 'ঢাবি নতুন আপডেট ও আসন বিন্যাস',
            'slug' => 'du-seat-plan-update-draft',
            'institution_name' => 'ঢাকা বিশ্ববিদ্যালয় (DU)',
            'university_type' => 'public',
            'academic_year' => '2026-2027',
            'source_url' => 'https://students.bd/du-seat-plan',
            'description' => '<p>সিট প্ল্যান প্রকাশিত হয়েছে।</p>',
            'status' => 'draft',
            'views_count' => 15,
        ]);

        AdmissionNotice::create([
            'title' => 'জিএসটি গুচ্ছ ভর্তি ২০২৬',
            'slug' => 'gst-published-main',
            'institution_name' => 'জিএসটি (General, Science and Technology) গুচ্ছ ভর্তি',
            'university_type' => 'guccho',
            'academic_year' => '2026-2027',
            'description' => '<p>জিএসটি গুচ্ছ প্রধান সার্কুলার।</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        AdmissionNotice::create([
            'title' => 'জিএসটি নতুন বিজ্ঞপ্তি ড্রাফট',
            'slug' => 'gst-draft-dup',
            'institution_name' => 'জিএসটি গুচ্ছ বিশ্ববিদ্যালয় (GST Cluster)',
            'university_type' => 'guccho',
            'academic_year' => '2026-2027',
            'description' => '<p>জিএসটি গুচ্ছ ডুপ্লিকেট ড্রাফট।</p>',
            'status' => 'draft',
        ]);

        /** @var AdmissionScraperService $scraper */
        $scraper = app(AdmissionScraperService::class);
        $summary = $scraper->mergeDuplicates();

        $this->assertSame(2, $summary['groups_merged']);
        $this->assertSame(2, $summary['deleted_count']);
        $this->assertSame(2, $summary['remaining_count']);

        $duPrimary->refresh();
        $this->assertSame(65, $duPrimary->views_count);
        $this->assertContains('du-seat-plan-update-draft', $duPrimary->merged_slugs);
        $this->assertNotEmpty($duPrimary->update_history);

        // Merged old slug returns 301 redirect to canonical slug
        $this->get('/admission-notice/du-seat-plan-update-draft')
            ->assertStatus(301)
            ->assertRedirect('/admission-notice/dhaka-university-circular-main');

        // Live scraper skips already recorded source_url and merges new update without adding a duplicate row
        $rssXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"><channel>
    <item>
        <title>ঢাবি নতুন আপডেট ও আসন বিন্যাস</title>
        <link>https://students.bd/du-seat-plan</link>
        <description>ডুপ্লিকেট আইটেম</description>
    </item>
    <item>
        <title>ঢাকা বিশ্ববিদ্যালয় ভর্তি পরীক্ষার নতুন তারিখ ঘোষণা</title>
        <link>https://students.bd/du-new-exam-date</link>
        <description>ঢাকা বিশ্ববিদ্যালয় ভর্তি পরীক্ষার তারিখ পরিবর্তন সংক্রান্ত বিজ্ঞপ্তি।</description>
    </item>
</channel></rss>
XML;

        Http::fake([
            'https://students.bd/rss.xml' => Http::response($rssXml, 200),
        ]);

        $imported = $scraper->scrapeLiveFeeds();
        $this->assertSame(1, $imported);
        $this->assertSame(2, AdmissionNotice::count());

        $duPrimary->refresh();
        $titles = collect($duPrimary->update_history)->pluck('title')->all();
        $this->assertContains('ঢাকা বিশ্ববিদ্যালয় ভর্তি পরীক্ষার নতুন তারিখ ঘোষণা', $titles);
    }

    public function test_flexible_coupon_enforces_type_product_user_and_usage_limits(): void
    {
        $allowedStudent = $this->makeStudent();
        $otherStudent = $this->makeStudent();
        $bookAllowed = $this->makeBook(300, 'Allowed Book');
        $bookBlocked = $this->makeBook(300, 'Blocked Book');
        $course = $this->makeCourse(500);
        $plan = $this->makePlan(200);

        app(WalletService::class)->credit($allowedStudent, 2000, 'test', 'INIT', 'Seed');

        // 1. Course-only discount coupon (20% off, max 150)
        $courseCoupon = Coupon::create([
            'name' => 'Course 20%',
            'code' => 'COURSE20',
            'coupon_kind' => 'discount',
            'discount_type' => 'percent',
            'discount_value' => 20,
            'max_discount_amount' => 150,
            'applicable_types' => ['course'],
            'is_active' => true,
        ]);

        $this->actingAs($allowedStudent, 'web')
            ->postJson('/coupons/apply', [
                'code' => 'course20',
                'target_type' => 'book',
                'target_id' => $bookAllowed->id,
            ])
            ->assertStatus(422);

        $this->actingAs($allowedStudent, 'web')
            ->postJson('/coupons/apply', [
                'code' => 'course20',
                'target_type' => 'course',
                'target_id' => $course->id,
            ])
            ->assertOk()
            ->assertJsonPath('valid', true)
            ->assertJsonPath('coupon.discount_amount', 100)
            ->assertJsonPath('coupon.final_amount', 400);

        // 2. Product-wise + User-wise + per-user limit coupon (100% off fixed)
        $vipCoupon = Coupon::create([
            'name' => 'VIP Book Free',
            'code' => 'VIPBOOKFREE',
            'coupon_kind' => 'discount',
            'discount_type' => 'fixed',
            'discount_value' => 500,
            'applicable_types' => ['book'],
            'applicable_product_ids' => ['book' => [$bookAllowed->id]],
            'allowed_student_ids' => [$allowedStudent->id],
            'max_uses' => 2,
            'max_uses_per_user' => 1,
            'is_active' => true,
        ]);

        // Blocked for unlisted user
        $this->actingAs($otherStudent, 'web')
            ->postJson('/coupons/apply', [
                'code' => 'VIPBOOKFREE',
                'target_type' => 'book',
                'target_id' => $bookAllowed->id,
            ])
            ->assertStatus(422);

        // Blocked for different book
        $this->actingAs($allowedStudent, 'web')
            ->postJson('/coupons/apply', [
                'code' => 'VIPBOOKFREE',
                'target_type' => 'book',
                'target_id' => $bookBlocked->id,
            ])
            ->assertStatus(422);

        // Accepted 100%-off checkout via 'coupon' method
        $result = app(PurchaseService::class)->purchaseBook($allowedStudent, $bookAllowed, 'coupon', null, 'VIPBOOKFREE');
        $this->assertSame('completed', $result['order']->status);
        $this->assertEquals(0.0, (float) $result['order']->amount);
        $this->assertTrue($allowedStudent->bookShelf()->where('book_id', $bookAllowed->id)->exists());

        $vipCoupon->refresh();
        $this->assertSame(1, $vipCoupon->uses_count);

        // Per-user limit (max_uses_per_user = 1) blocks second attempt
        $this->actingAs($allowedStudent, 'web')
            ->postJson('/coupons/apply', [
                'code' => 'VIPBOOKFREE',
                'target_type' => 'book',
                'target_id' => $bookAllowed->id,
            ])
            ->assertStatus(422);

        // 3. Plan + Invoice coupon test
        $planCoupon = Coupon::create([
            'name' => 'Flat 50 Off',
            'code' => 'FLAT50',
            'coupon_kind' => 'discount',
            'discount_type' => 'fixed',
            'discount_value' => 50,
            'applicable_types' => ['plan', 'invoice'],
            'max_uses' => 1,
            'is_active' => true,
        ]);

        $planResult = app(PurchaseService::class)->purchaseSubscription($allowedStudent, $plan, 1, 'wallet', null, 'FLAT50');
        $this->assertSame('completed', $planResult['order']->status);
        $this->assertEquals(150.0, (float) $planResult['order']->amount);

        // Invoice created for that order records discount + original subtotal
        $orderInvoice = Invoice::where('order_id', $planResult['order']->id)->first();
        $this->assertNotNull($orderInvoice);
        $this->assertEquals(200.0, (float) $orderInvoice->subtotal);
        $this->assertEquals(50.0, (float) $orderInvoice->discount);
        $this->assertEquals(150.0, (float) $orderInvoice->total);

        // Global max_uses = 1 blocks invoice usage of the same coupon
        $manualInv = app(InvoiceService::class)->createManual($allowedStudent->id, [
            ['description' => 'Course Fee', 'qty' => 1, 'unit_price' => 500],
        ], 'sent', null, null, 0, 0, ['course_id' => $course->id]);

        $this->actingAs($allowedStudent, 'web')
            ->post("/invoices/{$manualInv->id}/pay", [
                'payment_method' => 'wallet',
                'coupon_code' => 'FLAT50',
            ])
            ->assertSessionHas('error');

        // Fresh invoice coupon pays & auto-enrolls course
        Coupon::create([
            'name' => 'Full Invoice Waiver',
            'code' => 'INV100',
            'coupon_kind' => 'discount',
            'discount_type' => 'percent',
            'discount_value' => 100,
            'applicable_types' => ['invoice'],
            'applicable_product_ids' => ['invoice' => [$manualInv->id]],
            'is_active' => true,
        ]);

        $this->actingAs($allowedStudent, 'web')
            ->post("/invoices/{$manualInv->id}/pay", [
                'payment_method' => 'coupon',
                'coupon_code' => 'INV100',
            ])
            ->assertSessionHas('success');

        $manualInv->refresh();
        $this->assertSame('paid', $manualInv->status);
        $this->assertEquals(0.0, (float) $manualInv->total);
        $this->assertTrue($allowedStudent->courseEnrollments()->where('course_id', $course->id)->exists());
    }

    public function test_admin_can_create_edit_recur_duplicate_and_delete_flexible_invoices(): void
    {
        $admin = $this->makeAdmin();
        $studentA = $this->makeStudent(['name' => 'Student Alpha']);
        $studentB = $this->makeStudent(['name' => 'Student Beta']);

        $periodStart = now()->subDays(25)->toDateString();
        $periodEnd = now()->addDays(4)->toDateString();

        // Admin creates recurring invoice
        $this->actingAs($admin, 'admin')
            ->post('/admin/invoices', [
                'student_id' => $studentA->id,
                'type' => 'recurring',
                'status' => 'sent',
                'due_at' => now()->addDays(4)->toDateString(),
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'recurrence_interval' => 'monthly',
                'auto_generate_next' => true,
                'notify_student' => false,
                'discount' => 20,
                'vat_percent' => 0,
                'items' => [
                    ['description' => 'Monthly Mentorship', 'qty' => 1, 'unit_price' => 300],
                ],
            ])
            ->assertRedirect();

        $invoice = Invoice::firstOrFail();
        $this->assertSame('recurring', $invoice->type);
        $this->assertSame($studentA->id, $invoice->student_id);
        $this->assertEquals(280.0, (float) $invoice->total);
        $this->assertTrue((bool) ($invoice->meta['auto_generate_next'] ?? false));

        // Auto-generate next recurring cycle when period_end is within 7 days
        $generated = app(InvoiceService::class)->generateUpcomingRecurring(7);
        $this->assertSame(1, $generated);
        $this->assertSame(2, Invoice::count());

        // Second run must be idempotent
        $this->assertSame(0, app(InvoiceService::class)->generateUpcomingRecurring(7));

        // Admin edits unpaid invoice: changes student + switches type to one_time
        $this->actingAs($admin, 'admin')
            ->put("/admin/invoices/{$invoice->id}", [
                'student_id' => $studentB->id,
                'type' => 'one_time',
                'status' => 'sent',
                'discount' => 0,
                'vat_percent' => 0,
                'items' => [
                    ['description' => 'Updated Mentorship Fee', 'qty' => 1, 'unit_price' => 450],
                ],
            ])
            ->assertRedirect();

        $invoice->refresh();
        $this->assertSame($studentB->id, $invoice->student_id);
        $this->assertSame('one_time', $invoice->type);
        $this->assertEquals(450.0, (float) $invoice->total);

        // Admin duplicates and deletes unpaid invoice
        $this->actingAs($admin, 'admin')
            ->post("/admin/invoices/{$invoice->id}/duplicate")
            ->assertRedirect();
        $this->assertSame(3, Invoice::count());

        $this->actingAs($admin, 'admin')
            ->delete("/admin/invoices/{$invoice->id}")
            ->assertRedirect('/admin/invoices');
        $this->assertNull(Invoice::find($invoice->id));
    }
}
