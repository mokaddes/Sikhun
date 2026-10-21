<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AiProviderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:openai_compatible,anthropic_compatible,google_gemini,custom,openai,gemini,claude,groq,deepseek,ollama,vllm,huggingface'],
            'api_key' => ['nullable', 'string'],
            'model_name' => ['required', 'string', 'max:200'],
            'api_endpoint' => ['nullable', 'string', 'max:500'],
            'custom_headers' => ['nullable', 'array'],
            'custom_headers.*.name' => ['nullable', 'string', 'max:100'],
            'custom_headers.*.value' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'max_tokens' => ['required', 'integer', 'min:1'],
            'temperature' => ['required', 'numeric', 'min:0', 'max:2'],
            'use_cases' => ['required', 'array', 'min:1'],
            'use_cases.*' => ['in:book_chat,exam_gen,flashcard_gen,essay_grade,schedule_gen,notification_gen,support_bot,book_ocr,book_retrieval,book_structure,blog_translate,blog_rewrite'],
            'default_use_cases' => ['nullable', 'array'],
            'default_use_cases.*' => ['in:book_chat,exam_gen,flashcard_gen,essay_grade,schedule_gen,notification_gen,support_bot,book_ocr,book_retrieval,book_structure,blog_translate,blog_rewrite'],
        ];
    }
}
