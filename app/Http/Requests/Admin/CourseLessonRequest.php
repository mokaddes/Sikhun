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
            'text_content' => ['nullable', 'string'],
            'is_free_preview' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
