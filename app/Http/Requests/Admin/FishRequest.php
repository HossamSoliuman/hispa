<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FishRequest extends FormRequest
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
            'code' => 'required|max:255',
            'scientific_name' => 'required|max:255',
            'english_name' => 'required|max:255',
            'red_sea_name' => 'required|max:255',
            'arabian_gulf_name' => 'required|max:255',
            'region_id ' => 'nullable|exists:regions,id',
            'governorate_id  ' => 'nullable|exists:governorates,id',

        ];
    }
}
