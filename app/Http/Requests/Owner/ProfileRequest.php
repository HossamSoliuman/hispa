<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'id_number' => 'nullable|string|max:255',
            'record_number' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'fishing_license_number' => 'nullable|string|max:255',
            'fishing_license_expiry' => 'nullable|date',
            'region_id' => 'nullable|exists:regions,id',
            'governorate_id' => 'nullable|exists:governorates,id',
            'port_id' => 'nullable|exists:ports,id',
            'password' => 'nullable|min:6',
            'password_confirmation' => 'nullable|min:6|same:password',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }
}
