<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class CatchRequest extends FormRequest
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
            'trip_id' => 'required|exists:trips,id',
            'boat_id' => 'required|exists:boats,id',

            'fish_id' => 'required|array|min:1',
            'fish_id.*' => 'required|exists:fish,id',

            'weight' => 'required|array|min:1',
            'weight.*' => 'required|numeric|min:0.1',

            'price_per_kg' => 'required|array|min:1',
            'price_per_kg.*' => 'required|numeric|min:0',

            'total_price' => 'required|array|min:1',
            'total_price.*' => 'required|numeric|min:0.1',
        ];
    }

    public function messages(): array
    {
        return [
            'fish_id.required' => 'يجب اختيار نوع سمك واحد على الأقل',
            'fish_id.*.exists' => 'نوع السمك غير صحيح',

            'weight.*.required' => 'الوزن مطلوب',
            'weight.*.numeric' => 'الوزن يجب أن يكون رقمًا',

            'total_price.*.required' => 'السعر مطلوب',
            'total_price.*.numeric' => 'السعر يجب أن يكون رقمًا',

            'price_per_kg.*.required' => 'سعر الكيلو مطلوب',
            'price_per_kg.*.numeric' => 'سعر الكيلو يجب أن يكون رقمًا',
        ];
    }
}
