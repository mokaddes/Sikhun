<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\FinalizeChunkedUpload;
use App\Services\ChunkedUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Chunked upload endpoint for large book PDFs (200-300MB+), which would blow
 * past PHP's upload_max_filesize / post_max_size if sent in one request.
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
    /** Chunk and merge share this temp namespace on the private disk. */
    public const PREFIX = 'books';

    public const ALLOWED_EXTENSIONS = ['pdf'];

    private const MAX_FILE_SIZE = 500 * 1024 * 1024; // 500 MB final PDF

    public function __construct(private ChunkedUploadService $uploads) {}

    public function uploadChunk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:51200'], // 50 MB per chunk request
            'upload_id' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9\-]+$/'],
            'chunk_index' => ['required', 'integer', 'min:0', 'max:2048'],
            'total_chunks' => ['required', 'integer', 'min:1', 'max:2048'],
            'filename' => ['required', 'string', 'max:255', 'regex:/\.pdf$/i'],
        ]);

        if ((int) $validated['chunk_index'] >= (int) $validated['total_chunks']) {
            return response()->json(['ok' => false, 'message' => 'Invalid chunk index.'], 422);
        }

        $stored = $this->uploads->storeChunk(
            self::PREFIX,
            $validated['upload_id'],
            (int) $validated['chunk_index'],
            $request->file('file'),
            (int) $validated['total_chunks'],
            $validated['filename'],
        );

        // Answer honestly: claiming the chunk landed when the write was
        // rejected sends the browser on to merge() with pieces missing, where
        // the only symptom is an unexplained "upload incomplete".
        if (! $stored) {
            return response()->json([
                'ok' => false,
                'message' => 'The server could not write this chunk to storage. Check that storage/app/private exists and is writable by the web server.',
            ], 500);
        }

        return response()->json(['ok' => true, 'received' => (int) $validated['chunk_index']]);
    }

    public function mergeChunks(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'upload_id' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9\-]+$/'],
        ]);

        $uploadId = $validated['upload_id'];

        // Retry of an upload an earlier run already finished (the client's
        // first request timed out but the server kept working): hand back the
        // completed file instead of queueing a redundant merge.
        $existing = $this->uploads->mergeStatus(self::PREFIX, $uploadId);

        if ($existing['status'] === 'done'
            && $existing['temp_path']
            && Storage::disk('private')->exists($existing['temp_path'])) {
            return response()->json([
                'ok' => true,
                'temp_path' => $existing['temp_path'],
                'filename' => $existing['filename'],
                'size' => $existing['size'],
            ]);
        }

        // Otherwise merge in the background: a big concatenation can outrun
        // the PHP/proxy request timeout, which used to abort the request
        // while chunks sat finalized-but-unclaimed in temp/. The client now
        // polls books/merge-chunks/status/{upload_id} instead.
        $this->uploads->markMergeStatus(self::PREFIX, $uploadId, ['merge_status' => 'queued']);

        FinalizeChunkedUpload::dispatch(
            self::PREFIX,
            $uploadId,
            self::ALLOWED_EXTENSIONS,
            self::MAX_FILE_SIZE,
        );

        return response()->json(['ok' => true, 'status' => 'queued']);
    }

    public function mergeStatus(Request $request, string $uploadId): JsonResponse
    {
        if (! preg_match('/^[a-zA-Z0-9\-]+$/', $uploadId)) {
            return response()->json(['ok' => false, 'status' => 'error', 'message' => 'Invalid upload id.'], 422);
        }

        $status = $this->uploads->mergeStatus(self::PREFIX, $uploadId);

        if ($status['status'] === 'missing') {
            return response()->json(['ok' => false, 'status' => 'error', 'message' => $status['message']], 404);
        }

        return response()->json(array_merge(['ok' => $status['status'] === 'done'], $status));
    }
}
