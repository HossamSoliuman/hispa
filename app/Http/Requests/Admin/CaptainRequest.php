<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CaptainRequest extends FormRequest
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
        $id = $this->route('captain');

        return [
            'boat_id' => 'required|numeric|exists:boats,id',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'region_id' => 'required|exists:regions,id',
            'governorate_id' => 'required|exists:governorates,id',
            'port_id' => 'nullable|exists:ports,id',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
