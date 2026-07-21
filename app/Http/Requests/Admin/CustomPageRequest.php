<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomPageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $pageId = $this->route('page')?->id;

        return [
            'slug' => ['required', 'string', 'max:255', Rule::unique('custom_pages', 'slug')->ignore($pageId)],
            'title_bn' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'content_bn' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'meta_title_bn' => ['nullable', 'string', 'max:255'],
            'meta_title_en' => ['nullable', 'string', 'max:255'],
            'meta_description_bn' => ['nullable', 'string', 'max:500'],
            'meta_description_en' => ['nullable', 'string', 'max:500'],
            'is_published' => ['boolean'],
        ];
    }
}
