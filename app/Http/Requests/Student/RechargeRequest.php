<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class RechargeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:50', 'max:50000'],
            'method' => ['required', 'in:zinipay,manual'],
            'transaction_reference' => ['required_if:method,manual', 'nullable', 'string', 'max:255'],
        ];
    }
}
