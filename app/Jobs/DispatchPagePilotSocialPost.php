<?php

namespace App\Jobs;

use App\Models\AdmissionNotice;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\JobCircular;
use App\Services\Social\PagePilotSocialService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DispatchPagePilotSocialPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $type,
        public int $modelId
    ) {}

    public function handle(PagePilotSocialService $social): void
    {
        if (! $social->isAutoPublishEnabled()) {
            Log::info('PagePilotSocial: Auto-publish disabled in settings. Skipping.');
            return;
        }

        $result = match ($this->type) {
            'admission' => ($notice = AdmissionNotice::find($this->modelId)) ? $social->publishAdmissionNotice($notice) : null,
            'job' => ($job = JobCircular::find($this->modelId)) ? $social->publishJobCircular($job) : null,
            'blog' => ($post = BlogPost::find($this->modelId)) ? $social->publishBlogPost($post) : null,
            'course' => ($course = Course::find($this->modelId)) ? $social->publishCourse($course) : null,
            default => null,
        };

        if ($result) {
            Log::info('PagePilotSocial: Post published result', [
                'type' => $this->type,
                'model_id' => $this->modelId,
                'result' => $result,
            ]);
        }
    }
}
