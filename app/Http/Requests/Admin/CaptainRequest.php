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
        $captain = \App\Models\User::find($id);

        $rules = [
            'boat_id' => 'required|numeric|exists:boats,id',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'name' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'region_id' => 'required|string|exists:regions,id',
            'governorate_id' => 'required|string|exists:governorates,id',
            'job_title' => 'required|string|max:255',
            'salary_type' => 'required|in:salary,percentage',
            'salary_amount' => 'required|numeric|min:0',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'IBAN' => 'required|string|max:50',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'fishing_license_number' => 'required|string|max:50',
            'fishing_license_expiry' => 'required|date',
            'driving_license_number' => 'required|string|max:50',
            'driving_license_expiry' => 'required|date',
        ];

        if ($this->nationality === 'سعودي') {
            $rules['id_number'] = 'required|string|max:50';
            $rules['id_attachment'] = [
                $captain && $captain->id_attachment ? 'nullable' : 'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:4096',
            ];
        } else {
            $rules['residence_number'] = 'required|string|max:50';
            $rules['attachment'] = [
                $captain && $captain->attachment ? 'nullable' : 'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:4096',
            ];
            $rules['passport_number'] = 'required|string|max:50';
            $rules['residence_start_date'] = 'required|date';
            $rules['residence_end_date'] = 'required|date|after_or_equal:residence_start_date';
        }

        return $rules;
    }
}
