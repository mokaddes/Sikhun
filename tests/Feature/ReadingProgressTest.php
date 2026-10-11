<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingSession;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Reader "resume where you left off": the last page is persisted per
 * student+book and handed back to the reader on reopen.
 */
class ReadingProgressTest extends TestCase
{
    use RefreshDatabase;

    private function student(): Student
    {
        return Student::create([
            'name' => 'Test Student',
            'email' => 'progress-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'status' => 'active',
            'referral_code' => 'REF'.strtoupper(uniqid()),
        ]);
    }

    private function freeBook(): Book
    {
        return Book::create([
            'title' => 'Physics',
            'slug' => 'physics-'.uniqid(),
            'price' => 0,
            'is_free' => true,
            'is_published' => true,
            'total_pages' => 50,
        ]);
    }

    private function paidBook(): Book
    {
        return Book::create([
            'title' => 'Chemistry',
            'slug' => 'chemistry-'.uniqid(),
            'price' => 100,
            'is_published' => true,
            'total_pages' => 50,
        ]);
    }

    public function test_catalog_save_progress_updates_last_page(): void
    {
        $student = $this->student();
        $book = $this->freeBook();

        ReadingSession::create([
            'student_id' => $student->id,
            'book_id' => $book->id,
            'pages_read' => 1,
            'ip_address' => '127.0.0.1',
        ]);

        $this->actingAs($student, 'web')
            ->post(route('reader.progress', $book), ['page' => 7])
            ->assertOk()
            ->assertJson(['saved' => true]);

        $this->assertSame(7, (int) ReadingSession::first()->last_page);
    }

    public function test_catalog_save_progress_ignores_inaccessible_page(): void
    {
        $student = $this->student();
        $book = $this->paidBook(); // not owned, no chapters → no page access

        ReadingSession::create([
            'student_id' => $student->id,
            'book_id' => $book->id,
            'pages_read' => 1,
            'ip_address' => '127.0.0.1',
        ]);

        $this->actingAs($student, 'web')
            ->post(route('reader.progress', $book), ['page' => 3])
            ->assertOk()
            ->assertJson(['saved' => false]);

        $this->assertNull(ReadingSession::first()->last_page);
    }

    public function test_catalog_show_returns_initial_page_from_history(): void
    {
        $student = $this->student();
        $book = $this->freeBook();

        ReadingSession::create([
            'student_id' => $student->id,
            'book_id' => $book->id,
            'pages_read' => 40,
            'last_page' => 42,
            'ip_address' => '127.0.0.1',
        ]);

        $this->actingAs($student, 'web')
            ->get(route('reader.show', $book))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('initialPage', 42));
    }

    public function test_catalog_show_defaults_to_first_page_without_history(): void
    {
        $student = $this->student();
        $book = $this->freeBook();

        $this->actingAs($student, 'web')
            ->get(route('reader.show', $book))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('initialPage', 1));
    }

    public function test_my_book_save_progress_updates_last_page(): void
    {
        $student = $this->student();
        $myBook = $student->myBooks()->create([
            'title' => 'My Notes',
            'file_path' => 'my-books/'.uniqid().'.pdf',
            'total_pages' => 10,
            'processing_status' => 'completed',
        ]);

        $this->actingAs($student, 'web')
            ->post(route('my-books.reader.progress', $myBook), ['page' => 4])
            ->assertOk()
            ->assertJson(['saved' => true]);

        $this->assertSame(4, (int) $myBook->fresh()->last_page);
    }

    public function test_my_book_show_returns_initial_page(): void
    {
        $student = $this->student();
        $myBook = $student->myBooks()->create([
            'title' => 'My Notes',
            'file_path' => 'my-books/'.uniqid().'.pdf',
            'total_pages' => 10,
            'last_page' => 6,
            'processing_status' => 'completed',
        ]);

        $this->actingAs($student, 'web')
            ->get(route('my-books.reader.show', $myBook))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('initialPage', 6));
    }
}
