<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class InspectionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'boat_id' => 'required|exists:boats,id',
            'status' => 'required',
            'check_date' => 'required|date',
            '$data' => 'date',
        ];
    }

    public function messages()
    {
        return [
            'boat_id.required' => 'يجب اختيار القارب',
            'boat_id.exists' => 'القارب غير موجود',
            'status.required' => 'يجب اختيار الحالة',
            'check_date.required' => 'تاريخ الفحص مطلوب',
            'check_date.date' => 'تاريخ الفحص يجب ان يكون بصيغة صحيحة',

        ];
    }
}
