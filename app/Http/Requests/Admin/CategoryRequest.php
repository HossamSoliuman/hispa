<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            //   'type' => 'nullable|string|max:255',
            'status' => 'required|integer',
            'parent_id' => 'required|integer|exists:categories,id',
        ];
    }

    public function messages()
    {
        return [
            'name_ar.required' => 'الاسم بالعربية مطلوب',
            'name_en.required' => 'الاسم بالإنجليزية مطلوب',
            // 'type.required' => 'النوع مطلوب',
            'status.required' => 'الحالة مطلوبة',
            'parent_id.required' => 'الصنف الرئيسي مطلوب',
        ];
    }
}
