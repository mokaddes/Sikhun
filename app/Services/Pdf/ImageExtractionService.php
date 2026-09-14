<?php

namespace App\Services\Pdf;

use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores image binaries extracted from PDFs onto the PRIVATE disk (same
 * policy as book PDFs themselves — book content is never public). URLs
 * are minted per-request through signed, access-checked endpoints.
 */
class ImageExtractionService
{
    public function storeExtractedImage(Book $book, string $binary, string $extension, int $pageNumber): ?string
    {
        $extension = preg_replace('/[^a-z0-9]/', '', strtolower($extension)) ?: 'png';

        if (! in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true)) {
            return null;
        }

        $path = "books/{$book->id}/images/p{$pageNumber}-".Str::random(10).'.'.$extension;

        if (! Storage::disk('private')->put($path, $binary)) {
            return null;
        }

        return $path;
    }

    /** Remove a book's extracted image directory on reprocess/delete. */
    public function purgeBookImages(Book $book): void
    {
        Storage::disk('private')->deleteDirectory("books/{$book->id}/images");
    }
}
