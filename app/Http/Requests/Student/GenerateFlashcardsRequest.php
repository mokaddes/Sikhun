<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class GenerateFlashcardsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'source_type' => ['nullable', 'string', 'in:topic,paragraph,book,question_bank'],
            'source_topic' => ['nullable', 'string', 'max:300'],
            'source_book_id' => ['nullable', 'exists:books,id'],
            'source_my_book_id' => ['nullable', 'integer', 'exists:my_books,id'],
            'chapter_ids' => ['nullable', 'array'],
            'chapter_ids.*' => ['integer', 'distinct'],
            'subject_id' => ['nullable', 'required_if:source_type,question_bank', 'integer', 'exists:question_subjects,id'],
            'origin_tag' => ['nullable', 'string', 'max:100'],
            'sector' => ['nullable', 'string', 'in:admission,job,hsc,ssc'],
            'source_text' => ['nullable', 'string', 'max:10000'],
            'count' => ['required', 'integer', 'min:5', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject_id.required_if' => 'প্রশ্নব্যাংক থেকে ফ্ল্যাশকার্ড তৈরি করতে একটি বিষয় নির্বাচন করুন।',
        ];
    }
}
