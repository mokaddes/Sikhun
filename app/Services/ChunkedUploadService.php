<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Generic chunked uploader for files too large to send in one request.
 *
 * A client slices a file into fixed-size chunks, POSTs each one with the same
 * `upload_id`, then calls merge() — which verifies every chunk arrived, streams
 * them into a single file, and validates size and magic bytes before handing
 * back a temp path that the caller moves into its permanent home.
 *
 * Chunks live under {prefix}/temp/{upload_id}/ on the private disk, so a
 * half-finished upload is never publicly reachable. Callers are responsible
 * for deleting stale temp directories (or calling cleanup()).
 */
class ChunkedUploadService
{
    public const CHUNK_SIZE = 5 * 1024 * 1024; // 5 MB per chunk

    public function __construct(private string $disk = 'private') {}

    public function tempDirectory(string $prefix, string $uploadId): string
    {
        return "{$prefix}/temp/{$uploadId}";
    }

    /**
     * Persist one chunk of an in-flight upload along with the metadata merge()
     * needs to reassemble it.
     *
     * @return bool  false when the chunk could not be written to disk
     */
    public function storeChunk(string $prefix, string $uploadId, int $index, UploadedFile $file, int $totalChunks, string $filename): bool
    {
        $storage = Storage::disk($this->disk);
        $dir = $this->tempDirectory($prefix, $uploadId);
        $chunkPath = "{$dir}/chunks/chunk_{$index}";
        $metaPath = "{$dir}/meta.json";

        $file->storeAs("{$dir}/chunks", "chunk_{$index}", $this->disk);

        // The disk is configured with throw=false, so a rejected write returns
        // false instead of raising. Without this check a full or unwritable
        // disk still answers "chunk received", and the failure only surfaces
        // much later at merge() as a vague missing-chunk error.
        if (! $storage->exists($chunkPath)) {
            return false;
        }

        $storage->put($metaPath, json_encode([
            'filename' => $filename,
            'total_chunks' => $totalChunks,
            'chunk_size' => $file->getSize(),
            'updated_at' => now()->toIso8601String(),
        ]));

        return $storage->exists($metaPath);
    }

    /**
     * Move a merged temp file into its permanent home.
     *
     * $tempPath comes from the client, so it is only accepted when it is a
     * direct child of a temp upload directory for this prefix — that check is
     * what stops a crafted path from relocating an arbitrary file.
     *
     * @param  list<string>  $allowedExtensions
     * @return string|null  the stored path, or null when the temp file is invalid
     */
    public function promote(string $prefix, string $tempPath, string $destinationDir, array $allowedExtensions): ?string
    {
        $pattern = '#^'.preg_quote($prefix, '#').'/temp/[A-Za-z0-9\-]{8,64}/[^/\\\\]+$#';

        if (! preg_match($pattern, $tempPath)) {
            return null;
        }

        $storage = Storage::disk($this->disk);

        if (! $storage->exists($tempPath)) {
            return null;
        }

        $extension = strtolower(pathinfo($tempPath, PATHINFO_EXTENSION));

        if (! in_array($extension, $allowedExtensions, true)) {
            return null;
        }

        $destination = trim($destinationDir, '/').'/'.Str::uuid().'.'.$extension;

        // The disk is configured with throw=false, so a failed move is silent —
        // verify before reporting success to the caller.
        if (! $storage->move($tempPath, $destination) || ! $storage->exists($destination)) {
            return null;
        }

        // Sweep the now-empty temp upload directory (meta.json, stray chunks).
        $storage->deleteDirectory(dirname($tempPath));

        return $destination;
    }

    public function cleanup(string $prefix, string $uploadId): void
    {
        Storage::disk($this->disk)->deleteDirectory($this->tempDirectory($prefix, $uploadId));
    }

    /**
     * Concatenate a complete upload into {prefix}/temp/{upload_id}/{filename}.
     *
     * @param  list<string>  $allowedExtensions  e.g. ['mp4', 'webm']
     * @return array{ok: bool, message?: string, temp_path?: string, filename?: string, size?: int, missing?: list<int>}
     */
    public function merge(string $prefix, string $uploadId, array $allowedExtensions, int $maxBytes): array
    {
        $storage = Storage::disk($this->disk);
        $dir = $this->tempDirectory($prefix, $uploadId);
        $chunksDir = "{$dir}/chunks";
        $metaPath = "{$dir}/meta.json";

        if (! $storage->exists($metaPath)) {
            return ['ok' => false, 'message' => 'Upload not found. Try again.'];
        }

        $meta = json_decode((string) $storage->get($metaPath), true) ?: [];
        $totalChunks = (int) ($meta['total_chunks'] ?? 0);
        $filename = basename((string) ($meta['filename'] ?? 'upload'));

        if ($totalChunks < 1 || $totalChunks > 4096) {
            $this->cleanup($prefix, $uploadId);

            return ['ok' => false, 'message' => 'Invalid upload metadata.'];
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (! in_array($extension, $allowedExtensions, true)) {
            $this->cleanup($prefix, $uploadId);

            return ['ok' => false, 'message' => 'Unsupported file type.'];
        }

        $mergedPath = "{$dir}/{$filename}";

        // A previous merge() can finish after the browser gave up waiting —
        // concatenating a few hundred MB can outlive the client's request
        // timeout. merge() deletes the chunk directory as its final step, so
        // "merged file present, chunks gone" means that earlier run completed
        // and this is a retry: hand back the finished file instead of reporting
        // the chunks it already consumed as missing, which would force the
        // admin to re-upload the whole file.
        if ($storage->exists($mergedPath) && ! $storage->exists($chunksDir)) {
            $size = (int) $storage->size($mergedPath);

            if ($size >= 1024 && $size <= $maxBytes && $this->looksLike($mergedPath, $extension)) {
                return ['ok' => true, 'temp_path' => $mergedPath, 'filename' => $filename, 'size' => $size];
            }
        }

        // Verify every chunk arrived before merging.
        $missing = [];
        for ($i = 0; $i < $totalChunks; $i++) {
            if (! $storage->exists("{$chunksDir}/chunk_{$i}")) {
                $missing[] = $i;
            }
        }

        if ($missing) {
            return [
                'ok' => false,
                'message' => 'Upload incomplete — '.count($missing).' missing chunk(s). Start the upload again.',
                'missing' => array_slice($missing, 0, 20),
            ];
        }

        // Concatenate chunks in order, streaming to avoid loading into memory.
        $out = fopen($storage->path($mergedPath), 'wb');

        if ($out === false) {
            return ['ok' => false, 'message' => 'Failed to open output file.'];
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
            $this->cleanup($prefix, $uploadId);

            return ['ok' => false, 'message' => 'Merge failed: '.$e->getMessage()];
        }

        fclose($out);

        $size = $storage->size($mergedPath);

        if ($size < 1024 || $size > $maxBytes) {
            $this->cleanup($prefix, $uploadId);

            return ['ok' => false, 'message' => 'Merged file is outside the allowed size range.'];
        }

        if (! $this->looksLike($storage->path($mergedPath), $extension)) {
            $this->cleanup($prefix, $uploadId);

            return ['ok' => false, 'message' => 'The uploaded file does not look like a valid '.strtoupper($extension).' file.'];
        }

        // Drop the chunk pieces, keep only the merged temp file.
        $storage->deleteDirectory($chunksDir);

        return ['ok' => true, 'temp_path' => $mergedPath, 'filename' => $filename, 'size' => $size];
    }

    /**
     * Cheap magic-byte sniff so a renamed .exe cannot be stored as a .mp4.
     * Extensions without a known signature fall through to true — the
     * allowlist has already vetted them.
     */
    private function looksLike(string $path, string $extension): bool
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return false;
        }

        $head = (string) fread($handle, 12);
        fclose($handle);

        return match ($extension) {
            'pdf' => str_starts_with($head, '%PDF-'),
            // MP4/MOV family: a `ftyp` box starts at byte 4.
            'mp4', 'm4v', 'mov' => substr($head, 4, 4) === 'ftyp',
            // Matroska/WebM EBML header.
            'webm', 'mkv' => str_starts_with($head, "\x1A\x45\xDF\xA3"),
            default => true,
        };
    }
}
