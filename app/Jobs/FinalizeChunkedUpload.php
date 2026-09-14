<?php

namespace App\Jobs;

use App\Services\ChunkedUploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Runs a chunked-upload merge in the queue so a multi-hundred-MB file
 * concatenation cannot be killed by a web/PHP/proxy request timeout.
 *
 * The HTTP endpoint hands back {"status":"queued"} immediately and the
 * client polls ChunkedUploadService::mergeStatus() until this job records
 * 'done'/'error' (via markMergeStatus on the same meta.json the chunks
 * use). A slow disk or huge PDF only delays the merge, never aborts it.
 */
class FinalizeChunkedUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 1;

    public function __construct(
        private string $prefix,
        private string $uploadId,
        private array $allowedExtensions,
        private int $maxBytes,
    ) {
        $this->onQueue('default');
    }

    public function handle(ChunkedUploadService $uploads): void
    {
        $uploads->markMergeStatus($this->prefix, $this->uploadId, ['merge_status' => 'merging']);

        $result = $uploads->merge($this->prefix, $this->uploadId, $this->allowedExtensions, $this->maxBytes);

        if (! $result['ok']) {
            // Fatal errors inside merge() wipe the temp dir entirely, so
            // re-write meta.json with the real reason for the client.
            $uploads->markMergeStatus($this->prefix, $this->uploadId, [
                'merge_status' => 'error',
                'merge_message' => $result['message'] ?? 'Merge failed.',
            ]);

            return;
        }

        $uploads->markMergeStatus($this->prefix, $this->uploadId, [
            'merge_status' => 'done',
            'temp_path' => $result['temp_path'],
            'filename' => $result['filename'],
            'size' => $result['size'],
        ]);
    }

    public function failed(\Throwable $e): void
    {
        try {
            app(ChunkedUploadService::class)->markMergeStatus($this->prefix, $this->uploadId, [
                'merge_status' => 'error',
                'merge_message' => mb_substr($e->getMessage(), 0, 500),
            ]);
        } catch (\Throwable) {
            // No meta to write — the poller will report "upload not found".
        }
    }
}