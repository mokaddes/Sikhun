<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Chunked upload handler for large book PDFs (200-300MB+).
 *
 * Flow:
 *   POST /admin/books/upload-chunk  → save a single chunk
 *   POST /admin/books/merge-chunks  → concatenate chunks, validate, return temp path
 *   (then BookController::store/update moves the temp file into books/pdfs)
 *
 * Chunks live under storage/app/private/books/temp/{upload_id}/ so they are
 * never publicly accessible. A scheduled job sweeps stale uploads.
 */
class BookUploadController extends Controller
{
    private const CHUNK_SIZE = 5 * 1024 * 1024; // 5 MB per chunk upload
    private const MAX_FILE_SIZE = 500 * 1024 * 1024; // 500 MB final PDF

    public function uploadChunk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:51200'], // 50 MB per chunk request
            'upload_id' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9\-]+$/'],
            'chunk_index' => ['required', 'integer', 'min:0', 'max:2048'],
            'total_chunks' => ['required', 'integer', 'min:1', 'max:2048'],
            'filename' => ['required', 'string', 'max:255', 'regex:/\.pdf$/i'],
        ]);

        $uploadId = $validated['upload_id'];
        $chunkIndex = (int) $validated['chunk_index'];
        $totalChunks = (int) $validated['total_chunks'];

        if ($chunkIndex >= $totalChunks) {
            return response()->json(['ok' => false, 'message' => 'Invalid chunk index.'], 422);
        }

        $file = $request->file('file');

        // Track the largest chunk size so merge can sanity-check the whole file.
        $chunkDir = "books/temp/{$uploadId}";
        $storage = Storage::disk('private');

        $file->storeAs("{$chunkDir}/chunks", "chunk_{$chunkIndex}", 'private');

        $meta = [
            'filename' => $validated['filename'],
            'total_chunks' => $totalChunks,
            'chunk_size' => $file->getSize(),
            'updated_at' => now()->toIso8601String(),
        ];
        $storage->put("{$chunkDir}/meta.json", json_encode($meta));

        return response()->json(['ok' => true, 'received' => $chunkIndex]);
    }

    public function mergeChunks(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'upload_id' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9\-]+$/'],
        ]);

        $uploadId = $validated['upload_id'];
        $storage = Storage::disk('private');
        $chunksDir = "books/temp/{$uploadId}/chunks";
        $metaPath = "books/temp/{$uploadId}/meta.json";

        if (! $storage->exists($metaPath)) {
            return response()->json(['ok' => false, 'message' => 'Upload not found. Try again.'], 422);
        }

        $meta = json_decode((string) $storage->get($metaPath), true);
        $totalChunks = (int) ($meta['total_chunks'] ?? 0);
        $filename = basename((string) ($meta['filename'] ?? 'book.pdf'));

        if ($totalChunks < 1 || $totalChunks > 2048) {
            $this->cleanup($uploadId);

            return response()->json(['ok' => false, 'message' => 'Invalid upload metadata.'], 422);
        }

        // Verify every chunk arrived before merging.
        $missing = [];
        for ($i = 0; $i < $totalChunks; $i++) {
            if (! $storage->exists("{$chunksDir}/chunk_{$i}")) {
                $missing[] = $i;
            }
        }

        if ($missing) {
            return response()->json([
                'ok' => false,
                'message' => 'Upload incomplete — '.count($missing).' missing chunk(s). Start the upload again.',
                'missing' => array_slice($missing, 0, 20),
            ], 422);
        }

        // Concatenate chunks in order, streaming to avoid loading into memory.
        $mergedPath = "books/temp/{$uploadId}/{$filename}";
        $out = fopen($storage->path($mergedPath), 'wb');

        if ($out === false) {
            return response()->json(['ok' => false, 'message' => 'Failed to open output file.'], 500);
        }

        try {
            for ($i = 0; $i < $totalChunks; $i++) {
                $in = fopen($storage->path("{$chunksDir}/chunk_{$i}"), 'rb');

                if ($in === false) {
                    throw new \RuntimeException("Cannot open chunk {$i}.");
                }

                stream_copy_to_stream($in, $out);
                fclose($in);
            }
        } catch (\Throwable $e) {
            fclose($out);
            $this->cleanup($uploadId);

            return response()->json(['ok' => false, 'message' => 'Merge failed: '.$e->getMessage()], 500);
        }

        fclose($out);

        $size = $storage->size($mergedPath);

        if ($size < 1024 || $size > self::MAX_FILE_SIZE) {
            $this->cleanup($uploadId);

            return response()->json(['ok' => false, 'message' => 'Merged file is outside the allowed size range.'], 422);
        }

        // Lightweight PDF magic-byte check (must start with %PDF).
        $handle = fopen($storage->path($mergedPath), 'rb');
        $head = $handle !== false ? fread($handle, 5) : '';
        if ($handle !== false) {
            fclose($handle);
        }

        if ($head !== '%PDF-') {
            $this->cleanup($uploadId);

            return response()->json(['ok' => false, 'message' => 'The uploaded file is not a valid PDF.'], 422);
        }

        // Drop the chunk pieces, keep only the merged temp file.
        $storage->deleteDirectory($chunksDir);

        return response()->json([
            'ok' => true,
            'temp_path' => $mergedPath,
            'size' => $size,
            'filename' => $filename,
        ]);
    }

    private function cleanup(string $uploadId): void
    {
        Storage::disk('private')->deleteDirectory("books/temp/{$uploadId}");
    }
}