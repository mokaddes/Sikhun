<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $courseId = $this->route('course')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('courses', 'slug')->ignore($courseId)],
            'description' => ['nullable', 'string', 'max:5000'],
            'mentor_id' => ['nullable', 'exists:mentors,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'level' => ['nullable', 'in:ssc,hsc,university,job'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
