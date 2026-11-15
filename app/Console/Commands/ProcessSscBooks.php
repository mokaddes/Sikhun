<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBookPdf;
use App\Models\Book;
use App\Services\BookReaderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ProcessSscBooks extends Command
{
    protected $signature = 'books:process-ssc-new';
    protected $description = 'Attach transferred PDFs to Books #14-#19 and queue OCR/RAG processing';

    public function handle(BookReaderService $reader): int
    {
        $booksMap = [
            14 => ['file' => 'ssc_agriculture_2026.pdf', 'title' => 'কৃষিশিক্ষা'],
            15 => ['file' => 'ssc_bangla_grammar_2026.pdf', 'title' => 'বাংলা ব্যাকরণ ও নির্মিতি'],
            16 => ['file' => 'ssc_bangla_sahitto_2026.pdf', 'title' => 'বাংলা সাহিত্য'],
            17 => ['file' => 'ssc_bangla_sohopath_2026.pdf', 'title' => 'বাংলা সহপাঠ'],
            18 => ['file' => 'ssc_bgs_2026.pdf', 'title' => 'বাংলাদেশ ও বিশ্বপরিচয়'],
            19 => ['file' => 'ssc_bhugol_2026.pdf', 'title' => 'ভূগোল ও পরিবেশ'],
        ];

        foreach ($booksMap as $id => $info) {
            $book = Book::find($id);
            if (!$book) {
                $this->warn("Book #{$id} not found.");
                continue;
            }

            $relPath = "books/pdfs/{$info['file']}";
            if (!Storage::disk('private')->exists($relPath)) {
                $this->error("PDF file not found on private disk: {$relPath}");
                continue;
            }

            $absPath = Storage::disk('private')->path($relPath);
            $pages = $reader->detectPageCount($absPath);

            $this->info("Attaching #{$id} ({$info['title']}): {$pages} pages -> {$relPath}");

            $book->forceFill([
                'pdf_path' => $relPath,
                'total_pages' => $pages ?: $book->total_pages,
                'pdf_content_hash' => null,
                'processing_status' => 'pending',
                'processing_error' => null,
                'processing_started_at' => null,
                'processing_completed_at' => null,
                'is_published' => true,
            ])->save();

            ProcessBookPdf::dispatch($book->id)->onQueue('default');
            $this->line("  Queued ProcessBookPdf for book #{$id} successfully.");
        }

        $this->info("All 6 new SSC books queued for OCR and RAG processing!");
        return 0;
    }
}
