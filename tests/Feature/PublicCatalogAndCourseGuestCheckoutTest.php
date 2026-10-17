<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Course;
use App\Models\Order;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicCatalogAndCourseGuestCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_browse_library_and_book_detail_pages_without_auth(): void
    {
        $book = Book::create([
            'title' => 'HSC Physics 1st Paper',
            'slug' => 'hsc-physics-1st-paper',
            'price' => 150,
            'is_free' => false,
            'is_published' => true,
        ]);

        $this->get('/library')->assertStatus(200);
        $this->get('/library/'.$book->slug)->assertStatus(200);
    }

    public function test_library_actions_read_buy_and_add_to_shelf_strictly_require_auth(): void
    {
        $freeBook = Book::create([
            'title' => 'Free Bangla Grammar',
            'slug' => 'free-bangla-grammar',
            'price' => 0,
            'is_free' => true,
            'is_published' => true,
        ]);

        $paidBook = Book::create([
            'title' => 'Paid Higher Math',
            'slug' => 'paid-higher-math',
            'price' => 250,
            'is_free' => false,
            'is_published' => true,
            'chapter_purchase_enabled' => true,
        ]);

        $chapter = $paidBook->chapters()->create([
            'chapter_number' => '1',
            'title' => 'Matrices',
            'slug' => 'matrices',
            'level' => 1,
            'start_page' => 1,
            'end_page' => 10,
            'sort_order' => 1,
            'price' => 40,
        ]);

        // Guest attempting to Read, Add to Shelf, Buy Book, or Buy Chapter must be redirected to /login
        $this->get("/library/{$freeBook->id}/read")->assertRedirect('/login');
        $this->post("/library/{$freeBook->id}/shelf")->assertRedirect('/login');
        $this->post("/library/{$paidBook->id}/purchase", ['payment_method' => 'zinipay'])->assertRedirect('/login');
        $this->post("/library/{$paidBook->id}/chapters/{$chapter->id}/purchase", ['payment_method' => 'wallet'])->assertRedirect('/login');
    }

    public function test_guests_can_browse_courses_and_use_guest_checkout_for_free_and_paid_courses(): void
    {
        Mail::fake();

        $freeCourse = Course::create([
            'title' => 'Free Python Crash Course',
            'slug' => 'free-python-crash-course',
            'description' => 'Learn Python basics in Bengali.',
            'level' => 'hsc',
            'price' => 0,
            'is_active' => true,
            'delivery_type' => 'video',
        ]);

        $paidCourse = Course::create([
            'title' => 'Complete Web Development Bootcamp',
            'slug' => 'complete-web-development-bootcamp',
            'description' => 'Full-stack course.',
            'level' => 'university',
            'price' => 990,
            'is_active' => true,
            'delivery_type' => 'enrollment_link',
            'external_link' => 'https://drive.google.com/course-access',
        ]);

        // 1. Guests can browse /courses and /courses/{slug} without login
        $this->get('/courses')->assertStatus(200);
        $this->get('/courses/'.$freeCourse->slug)->assertStatus(200);
        $this->get('/courses/'.$paidCourse->slug)->assertStatus(200);

        // 2. Guest Checkout on Free Course creates Student, logs them in, and enrolls them
        $freeResp = $this->post("/courses/{$freeCourse->id}/guest-enroll", [
            'name' => 'Rahim Uddin',
            'email' => 'rahim@example.com',
            'phone' => '01700000000',
        ]);

        $freeResp->assertRedirect(route('courses.show', $freeCourse));
        $student = Student::where('email', 'rahim@example.com')->first();
        $this->assertNotNull($student);
        $this->assertTrue($student->courseEnrollments()->where('course_id', $freeCourse->id)->exists());

        // Logout to test Paid Course Guest Checkout as a fresh guest
        auth('web')->logout();

        Http::fake([
            '*/v1/payment/create' => Http::response([
                'status' => true,
                'invoice_id' => 'INV-GUEST-101',
                'payment_url' => 'https://secure.zinipay.com/payment/INV-GUEST-101',
            ], 200),
            '*/api/verify-payment' => Http::response([
                'status' => 'COMPLETED',
                'transaction_id' => 'TXN-GUEST-101',
            ], 200),
        ]);

        $paidResp = $this->post("/courses/{$paidCourse->id}/guest-enroll", [
            'name' => 'Karim Ahmed',
            'email' => 'karim@example.com',
            'phone' => '01800000000',
        ]);

        // Inertia::location or 302 redirect to ZiniPay payment URL
        $this->assertTrue(in_array($paidResp->getStatusCode(), [302, 409], true));

        $order = Order::where('orderable_type', 'course')->where('orderable_id', $paidCourse->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('INV-GUEST-101', $order->gateway_invoice_id);
        $this->assertTrue($order->meta['guest_checkout'] ?? false);

        // Simulate ZiniPay callback -> completes order and redirects back to the public course page
        $callbackResp = $this->get(route('wallet.gateway.success', ['order_number' => $order->order_number]));
        $callbackResp->assertRedirect(route('courses.show', $paidCourse->slug));

        $this->assertEquals('completed', $order->fresh()->status);
        $karim = Student::where('email', 'karim@example.com')->first();
        $this->assertTrue($karim->courseEnrollments()->where('course_id', $paidCourse->id)->exists());
    }
}
