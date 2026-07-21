<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MentorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $mentorId = $this->route('mentor')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('mentors', 'slug')->ignore($mentorId)],
            'designation' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'expertise' => ['nullable', 'string'], // comma-separated in the form, split in controller
        ];
    }
}
