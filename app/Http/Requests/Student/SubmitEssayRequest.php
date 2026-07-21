<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class SubmitEssayRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'grading_type' => ['required', 'in:hsc_bangla,hsc_english,general,custom_rubric'],
            'essay_text' => ['required', 'string', 'min:100'],
        ];
    }
}
