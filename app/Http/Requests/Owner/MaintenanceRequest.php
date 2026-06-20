<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class MaintenanceRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'next_maintenance_date' => 'nullable|date|after_or_equal:date',
            'estimated_cost' => 'required|numeric',
            'technician' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'boat_id.required' => 'يجب اختيار القارب',
            'boat_id.exists' => 'القارب غير موجود',
            'category_id.required' => 'يجب اختيار نوع الصيانة',
            'category_id.exists' => 'نوع الصيانة غير موجود',
            'date.required' => 'تاريخ الصيانة مطلوب',
            'next_maintenance_date.after_or_equal' => 'تاريخ الصيانة القادمة يجب أن يكون بعد تاريخ الصيانة',
            'estimated_cost.required' => 'التكلفة المتوقعة مطلوبة',
            'technician.required' => 'اسم الفني مطلوب',
        ];
    }
}
