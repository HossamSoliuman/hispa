<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'key' => 'required',
            'type' => 'required',
            'text' => 'required_if:type,==,text',
            'integer' => 'required_if:type,==,integer',
            'decimal' => 'required_if:type,==,decimal',
            'boolean' => 'required_if:type,==,boolean',
            'color' => 'required_if:type,==,color',
            'image' => 'required_if:type,image|image|max:1024', // Image validation and size limit

        ];
    }
}
