<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'coupon_kind' => ['nullable', 'in:discount,access_grant'],
            'discount_type' => ['nullable', 'in:percent,fixed'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'applicable_types' => ['nullable', 'array'],
            'applicable_types.*' => ['in:all,book,course,plan,invoice'],
            'applicable_product_ids' => ['nullable', 'array'],
            'applicable_product_ids.book' => ['nullable', 'array'],
            'applicable_product_ids.book.*' => ['integer'],
            'applicable_product_ids.course' => ['nullable', 'array'],
            'applicable_product_ids.course.*' => ['integer'],
            'applicable_product_ids.plan' => ['nullable', 'array'],
            'applicable_product_ids.plan.*' => ['integer'],
            'applicable_product_ids.invoice' => ['nullable', 'array'],
            'applicable_product_ids.invoice.*' => ['integer'],
            'code' => [
                Rule::requiredIf(fn () => $this->input('coupon_kind', 'discount') === 'discount'),
                'nullable',
                'string',
                'max:40',
                Rule::unique('coupons', 'code')->ignore($this->coupon),
            ],
            'student_id' => ['nullable', 'exists:students,id'],
            'allowed_student_ids' => ['nullable', 'array'],
            'allowed_student_ids.*' => ['integer', 'exists:students,id'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'max_uses_per_user' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }
}
