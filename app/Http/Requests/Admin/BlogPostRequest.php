<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $postId = $this->route('blog')?->id;

        return [
            'title' => ['required', 'string', 'max:500'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blog_posts', 'slug')->ignore($postId),
            ],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'],
            'tags' => ['nullable', 'array', 'max:20'],
            'tags.*' => ['integer', 'distinct', 'exists:blog_tags,id'],
            'featured_image' => $this->hasFile('featured_image')
                ? ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120']
                : ['nullable'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:300'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
