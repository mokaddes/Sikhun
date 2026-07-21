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
            'source_book_id' => ['nullable', 'exists:books,id'],
            'source_text' => ['required_without:source_book_id', 'nullable', 'string', 'min:10', 'max:5000'],
            'count' => ['required', 'integer', 'min:5', 'max:30'],
        ];
    }
}
