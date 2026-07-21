<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $planId = $this->route('plan')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('plans', 'slug')->ignore($planId)],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'ai_chat_minutes' => ['required', 'integer', 'min:0'],
            'ai_exam_count' => ['required', 'integer', 'min:0'],
            'trial_ai_minutes' => ['required', 'integer', 'min:0'],
            'features' => ['nullable', 'string'], // newline-separated in the form
            'gift_book_ids' => ['nullable', 'array'],
            'gift_book_ids.*' => ['exists:books,id'],
            'is_active' => ['boolean'],
        ];
    }
}
