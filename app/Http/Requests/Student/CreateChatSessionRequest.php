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
            'title' => ['nullable', 'string', 'max:255'],
        ];
    }
}
