<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AiProviderController;
use App\Jobs\GenerateFlashcards;
use App\Models\AiProvider;
use App\Models\AiProviderUseCase;
use App\Models\Book;
use App\Models\FlashcardSet;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Models\Student;
use App\Services\Ai\FlashcardSourceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class FlashcardBookCoverageTest extends TestCase
{
    use RefreshDatabase;

    private function createStudent(): Student
    {
        return Student::create([
            'name' => 'Flashcard Student',
            'email' => 'flashcard-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'referral_code' => 'FLASH'.strtoupper(uniqid()),
        ]);
    }

    private function createBook(string $status = 'completed'): Book
    {
        return Book::create([
            'title' => 'Coverage Physics',
            'slug' => 'coverage-physics-'.uniqid(),
            'price' => 100,
            'is_published' => true,
            'processing_status' => $status,
            'chapter_purchase_enabled' => true,
        ]);
    }

    private function addChapterChunks(Book $book, string $title, int $chapterNumber, int $chunkCount = 1)
    {
        $chapter = $book->chapters()->create([
            'chapter_number' => (string) $chapterNumber,
            'title' => $title,
            'slug' => strtolower($title).'-'.uniqid(),
            'level' => 1,
            'start_page' => $chapterNumber * 10,
            'end_page' => $chapterNumber * 10 + 9,
            'sort_order' => $chapterNumber,
            'price' => 50,
        ]);

        for ($i = 0; $i < $chunkCount; $i++) {
            $book->chunks()->create([
                'chapter_id' => $chapter->id,
                'chunk_index' => ($chapterNumber * 100) + $i,
                'page_number' => $chapterNumber * 10 + $i,
                'content' => "{$title} content {$i}",
            ]);
        }

        return $chapter;
    }

    public function test_book_flashcards_sample_across_accessible_chapters_with_valid_provenance(): void
    {
        $book = $this->createBook();
        $first = $this->addChapterChunks($book, 'Mechanics', 1, 8);
        $second = $this->addChapterChunks($book, 'Thermodynamics', 2, 8);
        $student = $this->createStudent();
        $student->bookShelf()->create(['book_id' => $book->id, 'source' => 'purchased']);

        $source = app(FlashcardSourceService::class)->fromBook($book, $student);

        $this->assertSame($book->title, $source['source_label']);
        $this->assertStringContainsString('[chunk ', $source['content']);
        $this->assertStringContainsString('chapter Mechanics', $source['content']);
        $this->assertStringContainsString('chapter Thermodynamics', $source['content']);
        $this->assertCount(16, $source['references']);
        $this->assertSame($first->id, $source['references'][array_key_first($source['references'])]['chapter_id']);
        $this->assertSame(10, $source['references'][array_key_first($source['references'])]['page']);
        $this->assertSame($second->id, end($source['references'])['chapter_id']);
    }

    public function test_chapter_only_access_never_includes_unowned_book_content(): void
    {
        $book = $this->createBook();
        $owned = $this->addChapterChunks($book, 'Owned', 1);
        $private = $this->addChapterChunks($book, 'Private', 2);
        $student = $this->createStudent();
        $student->ownedChapters()->create([
            'book_id' => $book->id,
            'chapter_id' => $owned->id,
            'source' => 'purchased',
            'price' => 50,
            'purchased_at' => now(),
        ]);

        $source = app(FlashcardSourceService::class)->fromBook($book, $student);

        $this->assertStringContainsString('Owned content', $source['content']);
        $this->assertStringNotContainsString('Private content', $source['content']);
        $this->assertSame([$owned->id], array_values(array_unique(array_column($source['references'], 'chapter_id'))));
        $this->assertSame($private->id, $book->chapters()->where('title', 'Private')->value('id'));
    }

    public function test_unprocessed_and_empty_books_are_rejected_with_clear_errors(): void
    {
        $student = $this->createStudent();
        $unprocessed = $this->createBook('processing');
        $unprocessed->update(['is_free' => true]);

        try {
            app(FlashcardSourceService::class)->fromBook($unprocessed, $student);
            $this->fail('Unprocessed books should be rejected.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
            $this->assertStringContainsString('not fully processed', $exception->getMessage());
        }

        $empty = $this->createBook();
        $empty->update(['is_free' => true]);

        try {
            app(FlashcardSourceService::class)->fromBook($empty, $student);
            $this->fail('Books without chunks should be rejected.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
            $this->assertStringContainsString('no searchable text chunks', $exception->getMessage());
        }
    }

    public function test_chapter_selection_cannot_expand_access(): void
    {
        $book = $this->createBook();
        $owned = $this->addChapterChunks($book, 'Owned', 1);
        $private = $this->addChapterChunks($book, 'Private', 2);
        $student = $this->createStudent();
        $student->ownedChapters()->create([
            'book_id' => $book->id,
            'chapter_id' => $owned->id,
            'source' => 'purchased',
            'price' => 50,
            'purchased_at' => now(),
        ]);

        $this->expectException(HttpException::class);
        app(FlashcardSourceService::class)->fromBook($book, $student, [$private->id]);
    }

    public function test_ai_provider_page_exposes_recommendations_for_each_planned_use_case(): void
    {
        $response = app(AiProviderController::class)->index();
        $props = (new \ReflectionProperty($response, 'props'))->getValue($response);

        $this->assertSame(
            ['book_chat', 'exam_gen', 'flashcard_gen', 'essay_grade', 'schedule_gen', 'support_bot', 'book_ocr', 'blog_rewrite', 'question_analysis', 'question_structure', 'question_gen'],
            array_keys($props['recommendations']),
        );
        $this->assertContains('Google — Gemini Flash', $props['recommendations']['flashcard_gen']['models']);
    }

    public function test_question_bank_flashcard_source_extracts_full_answer_text_not_option_letter(): void
    {
        $subject = QuestionSubject::create([
            'name_bn' => 'তথ্য ও যোগাযোগ প্রযুক্তি',
            'name_en' => 'ICT',
            'slug' => 'ict-' . uniqid(),
            'segment' => 'both',
            'is_active' => true,
        ]);

        QuestionBank::create([
            'segment' => 'hsc',
            'subject_id' => $subject->id,
            'origin_tag' => 'HSC-ICT-CH1',
            'exam_name' => 'HSC ICT Chapter 1',
            'question_text' => 'মানুষের চিন্তা চেতনাকে যন্ত্রের মাধ্যমে প্রকাশ করার প্রযুক্তিটি কী?',
            'question_type' => 'mcq',
            'options' => [
                ['key' => 'A', 'text' => 'বায়োমেট্রিক্স'],
                ['key' => 'B', 'text' => 'ভার্চুয়াল রিয়েলিটি'],
                ['key' => 'C', 'text' => 'কৃত্রিম বুদ্ধিমত্তা'],
                ['key' => 'D', 'text' => 'রোবটিক্স'],
            ],
            'correct_option' => 'C',
            'meta' => ['ai_explanation' => 'মানুষের বুদ্ধিবৃত্তিক চিন্তা যন্ত্রে রূপান্তর করাই কৃত্রিম বুদ্ধিমত্তা।'],
            'status' => 'active',
        ]);

        $source = app(FlashcardSourceService::class)->fromQuestionBank($subject->id, 'HSC-ICT-CH1', 'hsc');

        $this->assertStringContainsString('সঠিক উত্তর (পূর্ণাঙ্গ টেক্সট): কৃত্রিম বুদ্ধিমত্তা', $source['content']);
        $this->assertStringContainsString('ব্যাখ্যা: মানুষের বুদ্ধিবৃত্তিক চিন্তা যন্ত্রে রূপান্তর করাই কৃত্রিম বুদ্ধিমত্তা।', $source['content']);
        $this->assertStringNotContainsString("সঠিক উত্তর (পূর্ণাঙ্গ টেক্সট): C\n", $source['content']);
    }

    public function test_question_bank_flashcard_source_throws_validation_exception_on_subject_exam_mismatch(): void
    {
        $ict = QuestionSubject::create([
            'name_bn' => 'আইসিটি',
            'name_en' => 'ICT',
            'slug' => 'ict-mismatch-' . uniqid(),
            'segment' => 'both',
            'is_active' => true,
        ]);

        $this->expectException(ValidationException::class);
        app(FlashcardSourceService::class)->fromQuestionBank($ict->id, 'NON-EXISTENT-EXAM', 'hsc');
    }

    public function test_generate_flashcards_job_skips_bare_option_letter_answers(): void
    {
        $student = $this->createStudent();

        $provider = AiProvider::create([
            'name' => 'Mock OpenAI',
            'type' => 'openai',
            'api_key' => 'test-key',
            'model_name' => 'gpt-4o-mini',
            'api_endpoint' => 'https://fake-ai.test/v1/chat/completions',
            'is_active' => true,
        ]);

        AiProviderUseCase::create([
            'ai_provider_id' => $provider->id,
            'use_case' => 'flashcard_gen',
            'is_default' => true,
        ]);

        Http::fake([
            'https://fake-ai.test/*' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'flashcards' => [
                                ['front' => 'Q1?', 'back' => 'C'],
                                ['front' => 'Q2?', 'back' => 'গ)'],
                                ['front' => 'মানুষের চিন্তা চেতনাকে যন্ত্রের মাধ্যমে প্রকাশ করার প্রযুক্তিটি কী?', 'back' => 'কৃত্রিম বুদ্ধিমত্তা'],
                            ],
                        ], JSON_UNESCAPED_UNICODE),
                    ],
                ]],
            ], 200),
        ]);

        $set = $student->flashcardSets()->create([
            'title' => 'ICT Test Set',
            'generation_status' => 'generating',
            'generation_data' => ['source_type' => 'question_bank', 'count' => 3],
        ]);

        (new GenerateFlashcards($set->id, 'Sample Q&A context', 3))->handle();

        $set->refresh();
        $this->assertSame('completed', $set->generation_status);
        $this->assertCount(1, $set->flashcards);
        $this->assertSame('কৃত্রিম বুদ্ধিমত্তা', $set->flashcards->first()->back);
    }

    public function test_flashcard_create_page_exposes_sector_and_subject_filtered_exams(): void
    {
        $student = $this->createStudent();

        $ict = QuestionSubject::create([
            'name_bn' => 'তথ্য ও যোগাযোগ প্রযুক্তি',
            'name_en' => 'ICT',
            'slug' => 'ict-create-' . uniqid(),
            'segment' => 'both',
            'is_active' => true,
        ]);

        $physics = QuestionSubject::create([
            'name_bn' => 'পদার্থবিজ্ঞান',
            'name_en' => 'Physics',
            'slug' => 'phy-create-' . uniqid(),
            'segment' => 'admission',
            'is_active' => true,
        ]);

        QuestionBank::create([
            'segment' => 'hsc',
            'subject_id' => $ict->id,
            'origin_tag' => 'HSC-ICT-BOARD',
            'exam_name' => 'HSC ICT Board',
            'question_text' => 'ICT Q',
            'question_type' => 'mcq',
            'options' => [['key' => 'A', 'text' => 'Opt 1']],
            'correct_option' => 'A',
            'status' => 'active',
        ]);

        QuestionBank::create([
            'segment' => 'hsc',
            'subject_id' => $physics->id,
            'origin_tag' => 'HSC-PHY-BOARD',
            'exam_name' => 'HSC Physics Board',
            'question_text' => 'Physics Q',
            'question_type' => 'mcq',
            'options' => [['key' => 'A', 'text' => 'Opt 2']],
            'correct_option' => 'A',
            'status' => 'active',
        ]);

        $response = $this->actingAs($student, 'web')
            ->get(route('flashcards.create-form'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Student/Flashcards/Create')
            ->has('qbSectors', 1)
            ->has('subjectsBySector.hsc', 2)
            ->has('subjectExams', 2)
            ->where('subjectExams.0.segment', 'hsc')
        );
    }
}
