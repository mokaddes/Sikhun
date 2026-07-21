<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class GenerateScheduleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'exam_date' => ['required', 'date', 'after:today'],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['string', 'max:100'],
            'hours_per_day' => ['required', 'integer', 'min:1', 'max:16'],
            'weak_subjects' => ['nullable', 'array'],
            'weak_subjects.*' => ['string', 'max:100'],
            'style' => ['required', 'in:balanced,intensive,relaxed'],
            'include_weekends' => ['boolean'],
        ];
    }
}
