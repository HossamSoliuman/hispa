<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50|unique:users,phone',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            // نوع المالك: صياد فردي أو مؤسسة
            'owner_type' => 'required|in:fisherman,company',
            'region_id' => 'nullable|exists:regions,id',
            'governorate_id' => 'nullable|exists:governorates,id',
            'port_id' => 'nullable|exists:ports,id',
            'status' => 'required|in:0,1',

            'add_subscription' => 'boolean',
            'package_id' => 'required_if:add_subscription,1|nullable|exists:subscription_packages,id',
            'start_date' => 'required_if:add_subscription,1|nullable|date',
            'coupon_code' => 'nullable|string|max:64',
            'pay_cash' => 'boolean',
            'payment_notes' => 'nullable|string|max:500',
        ];

        return $rules;
    }

    protected function passedValidation(): void
    {
        if ($this->boolean('add_subscription') && ! $this->filled('start_date')) {
            $this->merge(['start_date' => now()->format('Y-m-d')]);
        }
    }
}
