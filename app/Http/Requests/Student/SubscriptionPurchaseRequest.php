<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionPurchaseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'plan_id' => ['required_without:is_custom', 'nullable', 'exists:plans,id'],
            'is_custom' => ['nullable', 'boolean'],
            'custom_config' => ['nullable', 'array'],
            'months' => ['required', 'integer', 'min:1', 'max:12'],
            'payment_method' => ['required', 'in:wallet,zinipay,mhpay,free'],
        ];
    }
}
