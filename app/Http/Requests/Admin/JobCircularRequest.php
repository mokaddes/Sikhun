<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobCircularRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $jobId = $this->route('job')?->id;

        return [
            'title' => ['required', 'string', 'max:500'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('job_circulars', 'slug')->ignore($jobId),
            ],
            'organization' => ['required', 'string', 'max:300'],
            'category' => ['required', 'string', 'max:100'],
            'job_type' => ['nullable', 'string', 'max:100'],
            'vacancy' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'education_requirement' => ['nullable', 'string', 'max:500'],
            'experience_requirement' => ['nullable', 'string', 'max:500'],
            'deadline' => ['required', 'date'],
            'published_date' => ['nullable', 'date'],
            'description' => ['required', 'string'],
            'application_instructions' => ['nullable', 'string'],
            'apply_url' => ['nullable', 'url', 'max:1000'],
            'source_name' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:1000'],
            'is_featured' => ['nullable', 'boolean'],
        ];
    }
}
