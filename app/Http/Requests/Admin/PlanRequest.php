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
            'ai_vision_limit' => ['required', 'integer', 'min:0'],
            'upload_book_limit' => ['required', 'integer', 'min:0'],
            'monthly_reupload_limit' => ['required', 'integer', 'min:0'],
            'ai_exam_count' => ['required', 'integer', 'min:0'],
            'trial_ai_minutes' => ['required', 'integer', 'min:0'],
            'features' => ['nullable', 'string'], // newline-separated in the form
            'ai_provider_id' => ['nullable', 'integer', 'exists:ai_providers,id'],
            'ai_model' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
                        'is_free' => ['boolean'],
        ];
    }
}
