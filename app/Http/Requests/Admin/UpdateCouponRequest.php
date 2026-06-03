<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $coupon = $this->route('coupon');
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:64',
                Rule::unique('coupons', 'code')->ignore($coupon->id)->whereNull('deleted_at'),
            ],
            'type' => 'required|in:percentage,fixed',
            'value' => ['required', 'numeric', 'min:0', Rule::when($this->type === 'percentage', 'max:100')],
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'package_ids' => 'nullable|array',
            'package_ids.*' => 'integer|exists:subscription_packages,id',
            'is_active' => 'boolean',
            'description_ar' => 'nullable|string|max:500',
            'description_en' => 'nullable|string|max:500',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('value') && $this->type === 'percentage') {
            $this->merge(['value' => min(100, max(0, (float) $this->value))]);
        }
    }
}
