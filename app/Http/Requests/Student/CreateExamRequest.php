<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class CreateExamRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'source_type' => ['required', 'in:book,topic,paragraph'],
            'source_book_id' => ['required_if:source_type,book', 'nullable', 'exists:books,id'],
            'chapter_ids' => ['nullable', 'array', 'max:100'],
            'chapter_ids.*' => ['integer', 'distinct', 'min:1'],
            'source_text' => ['required_if:source_type,topic,paragraph', 'nullable', 'string', 'min:3', 'max:5000'],
            'type' => ['required', 'in:mcq,cq,short,true_false,fill_blank'],
            'count' => ['required', 'integer', 'min:5', 'max:50'],
            'duration' => ['required', 'integer', 'min:0', 'max:180'],
            'mode' => ['required', 'in:practice,exam'],
        ];
    }
}
