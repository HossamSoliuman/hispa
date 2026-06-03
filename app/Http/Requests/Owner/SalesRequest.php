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
            'payment_status' => 'required',
            'sale_datetime' => 'required|date_format:Y-m-d\TH:i',

            'fish_id' => 'required|array|min:1',
            'fish_id.*' => 'required|exists:fish,id',

            // 'weight' => 'required|array|min:1',
            // 'weight.*' => 'required|numeric|min:0.1',

            // 'price_per_kilo' => 'required|array|min:1',
            // 'price_per_kilo.*' => 'required|numeric|min:0.1',
        ];
    }

    public function messages(): array
    {
        return [
            'fish_id.required' => 'يجب اختيار نوع سمك واحد على الأقل',
            'fish_id.*.exists' => 'نوع السمك غير صحيح',

            'weight.*.required' => 'الوزن مطلوب',
            'weight.*.numeric' => 'الوزن يجب أن يكون رقمًا',

            'price_per_kilo.*.required' => 'السعر مطلوب',
            'price_per_kilo.*.numeric' => 'السعر يجب أن يكون رقمًا',
        ];
    }
}
