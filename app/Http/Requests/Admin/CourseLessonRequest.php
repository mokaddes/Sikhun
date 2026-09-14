<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CourseLessonRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:video,text,pdf'],
            'video_url' => ['nullable', 'string', 'max:500'],
            // The same shape ChunkedUploadService::promote() accepts before it
            // will move the temp file into courses/videos.
            'video_temp_path' => ['nullable', 'string', 'max:255', 'regex:/^courses\/temp\/[a-zA-Z0-9\-]{8,64}\/[^\/]+$/'],
            'remove_video' => ['boolean'],
            'pdf_file' => ['nullable', 'file', 'mimetypes:application/pdf', 'max:51200'],
            'text_content' => ['nullable', 'string'],
            'is_free_preview' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
