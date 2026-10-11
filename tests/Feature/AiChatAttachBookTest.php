<?php

namespace Tests\Feature;

use App\Models\AiSession;
use App\Models\Book;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression coverage for attaching a book to an AI chat session.
 *
 * The frontend sends only `book_id` when attaching a catalog book and only
 * `my_book_id` when attaching an upload. The controller previously read both
 * keys unconditionally, so the library-book path raised
 * "Undefined array key \"my_book_id\"" (HTTP 500).
 */
class AiChatAttachBookTest extends TestCase
{
    use RefreshDatabase;

    private function student(): Student
    {
        return Student::create([
            'name' => 'Test Student',
            'email' => 'attach-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'status' => 'active',
            'referral_code' => 'REF'.strtoupper(uniqid()),
        ]);
    }

    private function newSession(Student $student): AiSession
    {
        return $student->aiSessions()->create([
            'source_type' => 'none',
            'title' => 'New chat',
            'messages' => [],
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
        ]);
    }

    public function test_attaching_catalog_book_with_empty_my_book_id_succeeds(): void
    {
        $student = $this->student();
        $session = $this->newSession($student);
        $book = $this->freeBook();

        $response = $this->actingAs($student, 'web')
            ->patch(route('ai-chat.attach-book', $session), ['book_id' => $book->id]);

        $response->assertRedirect(route('ai-chat.show', $session));
        $response->assertSessionHasNoErrors();

        $session->refresh();
        $this->assertSame('book', $session->source_type);
        $this->assertSame($book->id, $session->source_book_id);
        $this->assertNull($session->source_my_book_id);
    }

    public function test_attaching_upload_with_only_my_book_id_succeeds(): void
    {
        $student = $this->student();
        $session = $this->newSession($student);

        $myBook = $student->myBooks()->create([
            'title' => 'My Notes',
            'file_path' => 'my-books/'.uniqid().'.pdf',
            'total_pages' => 3,
            'processing_status' => 'completed',
        ]);

        $response = $this->actingAs($student, 'web')
            ->patch(route('ai-chat.attach-book', $session), ['my_book_id' => $myBook->id]);

        $response->assertRedirect(route('ai-chat.show', $session));

        $session->refresh();
        $this->assertSame('upload', $session->source_type);
        $this->assertSame($myBook->id, $session->source_my_book_id);
        $this->assertNull($session->source_book_id);
    }

    public function test_attaching_without_any_book_is_rejected(): void
    {
        $student = $this->student();
        $session = $this->newSession($student);

        $this->actingAs($student, 'web')
            ->patch(route('ai-chat.attach-book', $session), [])
            ->assertStatus(422);
    }
}
