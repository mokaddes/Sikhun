<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class CreateChatSessionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'source_book_id' => ['nullable', 'exists:books,id'],
            'my_book_id' => ['nullable', 'exists:my_books,id'],
            'title' => ['nullable', 'string', 'max:255'],
        ];
    }
}
