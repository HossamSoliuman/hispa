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

            'fish_id' => 'required|array|min:1',
            'fish_id.*' => 'required|exists:fish,id',

            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|exists:units,id',

            'weight' => 'required|array|min:1',
            'weight.*' => 'required|numeric|min:0.1',
        ];
    }

    public function messages(): array
    {
        return [
            'fish_id.required' => 'يجب اختيار نوع سمك واحد على الأقل',
            'fish_id.*.exists' => 'نوع السمك غير صحيح',

            'unit_id.*.required' => 'يجب اختيار وحدة الوزن',
            'unit_id.*.exists' => 'وحدة الوزن غير صحيحة',

            'weight.*.required' => 'الوزن مطلوب',
            'weight.*.numeric' => 'الوزن يجب أن يكون رقمًا',
        ];
    }
}
