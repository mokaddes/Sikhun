<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $authorId = $this->route('author')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('authors', 'slug')->ignore($authorId)],
            'bio' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
