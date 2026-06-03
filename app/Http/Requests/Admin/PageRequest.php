<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
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
            'title_ar' => 'required|max:255',
            'title_en' => 'required|max:255',
            'body_ar' => 'required',
            'body_en' => 'required',
            //    'page_type'=>'required|integer',
            'status' => 'in:1,0',

        ];
    }
}
