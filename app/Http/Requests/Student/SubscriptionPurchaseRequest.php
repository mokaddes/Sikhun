<?php

namespace App\Http\Requests\Student;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->plan_id) {
                $plan = \App\Models\Plan::find($this->plan_id);
                if ($plan && $plan->is_free && $this->payment_method !== 'free') {
                    $validator->errors()->add('plan_id', 'The Free plan is free and does not require paid checkout.');
                }
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->header('X-Inertia')) {
            parent::failedValidation($validator);
        }

        throw new ValidationException($validator, response()->json([
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors()->messages(),
        ], 422));
    }
}
