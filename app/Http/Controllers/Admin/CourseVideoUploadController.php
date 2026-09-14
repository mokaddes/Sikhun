<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ChunkedUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Chunked upload endpoint for lesson videos, mirroring the book PDF flow
 * (BookUploadController) so multi-GB files are not capped by PHP's
 * upload_max_filesize / post_max_size.
 *
 * Flow:
 *   POST /admin/courses/video/chunk  → save a single 5 MB chunk
 *   POST /admin/courses/video/merge  → concatenate + validate, return temp path
 *   (then CourseLessonController moves the temp file into courses/videos)
 */
class CourseVideoUploadController extends Controller
{
    /** Chunk and merge share this temp namespace on the private disk. */
    public const PREFIX = 'courses';

    /** Extensions an admin may upload as a lesson video. */
    public const ALLOWED_EXTENSIONS = ['mp4', 'webm', 'mov'];

    private const MAX_FILE_SIZE = 2 * 1024 * 1024 * 1024; // 2 GB final video

    public function __construct(private ChunkedUploadService $uploads) {}

    public function uploadChunk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:51200'], // 50 MB per chunk request
            'upload_id' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9\-]+$/'],
            'chunk_index' => ['required', 'integer', 'min:0', 'max:4095'],
            'total_chunks' => ['required', 'integer', 'min:1', 'max:4096'],
            'filename' => ['required', 'string', 'max:255', 'regex:/\.(mp4|webm|mov)$/i'],
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

        $result = $this->uploads->merge(
            self::PREFIX,
            $validated['upload_id'],
            self::ALLOWED_EXTENSIONS,
            self::MAX_FILE_SIZE,
        );

        if (! $result['ok']) {
            return response()->json($result, 422);
        }

        return response()->json([
            'ok' => true,
            'temp_path' => $result['temp_path'],
            'size' => $result['size'],
            'filename' => $result['filename'],
        ]);
    }
}
