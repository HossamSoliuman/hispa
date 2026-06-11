<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class SalesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'trip_id' => 'required|exists:trips,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_status' => 'required|in:unpaid,partially_paid,paid',
            'sale_datetime' => 'required|date_format:Y-m-d\TH:i',

            'fish_id' => 'required|array|min:1',
            'fish_id.*' => 'required|exists:fish,id',

            'unit_id' => 'nullable|array',
            'unit_id.*' => 'nullable|exists:units,id',

            'weight' => 'required|array|min:1',
            'weight.*' => 'nullable|numeric|min:0',

            'price_per_kilo' => 'required|array|min:1',
            'price_per_kilo.*' => 'nullable|numeric|min:0',

            'paid_amount' => 'nullable|numeric|min:0|required_if:payment_status,partially_paid',
        ];
    }

    public function messages(): array
    {
        return [
            'fish_id.required' => 'يجب اختيار نوع سمك واحد على الأقل',
            'fish_id.*.exists' => 'نوع السمك غير صحيح',

            'weight.*.numeric' => 'الوزن يجب أن يكون رقمًا',

            'price_per_kilo.*.numeric' => 'السعر يجب أن يكون رقمًا',

            'paid_amount.required_if' => 'يجب إدخال المبلغ المدفوع',
            'paid_amount.numeric' => 'المبلغ المدفوع يجب أن يكون رقمًا',
        ];
    }
}
