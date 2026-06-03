<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'status' => 'integer',
            'icon' => 'nullable|image|max:2000',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم بطاقة الدفع مطلوب',
            'body.required' => 'محتوى  بطاقة الدفع مطلوب',
            'status.required' => 'حالة  بطاقة الدفع مطلوب',
            'image.required' => 'صورة  بطاقة الدفع مطلوب',
        ];
    }
}
