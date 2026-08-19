<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionPurchaseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'exists:plans,id'],
            'months' => ['required', 'integer', 'min:1', 'max:12'],
            'payment_method' => ['required', 'in:wallet,zinipay'],
        ];
    }
}
