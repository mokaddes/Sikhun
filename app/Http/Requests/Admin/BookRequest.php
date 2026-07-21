<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $bookId = $this->route('book')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('books', 'slug')->ignore($bookId)],
            'description' => ['nullable', 'string', 'max:5000'],
            'author_id' => ['nullable', 'exists:authors,id'],
            'publication_id' => ['nullable', 'exists:publications,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'subject' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'in:ssc,hsc,university,job'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_free' => ['boolean'],
            'total_pages' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],
            'is_premium_gift' => ['boolean'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'pdf_file' => ['nullable', 'mimes:pdf', 'max:51200'], // 50MB
        ];
    }
}
