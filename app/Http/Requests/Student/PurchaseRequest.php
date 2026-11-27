<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'in:wallet,mhpay,bkash,nagad,coupon'],
            'coupon_code' => ['nullable', 'string', 'max:64'],
        ];
    }
}
