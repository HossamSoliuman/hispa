<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('crew');
        $crew = $id ? User::find($id) : null;

        $rules = [
            'owner_id' => 'required|integer|exists:users,id',
            'boat_id' => 'required|numeric|exists:boats,id',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->when($crew, fn ($rule) => $rule->ignore($crew->id))],
            'name' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->when($crew, fn ($rule) => $rule->ignore($crew->id))],
            'region_id' => 'required|string|exists:regions,id',
            'governorate_id' => 'required|string|exists:governorates,id',
            'port_id' => 'required|string|exists:ports,id',
            'job_title' => 'required|string|max:255',
            'salary_type' => 'required|in:salary,percentage,mixed',
            'salary_amount' => 'nullable|numeric|min:0|required_if:salary_type,salary',
            'fixed_salary_amount' => 'nullable|numeric|min:0|required_if:salary_type,mixed',
            'percentage_amount' => 'nullable|numeric|min:0|max:100|required_if:salary_type,percentage|required_if:salary_type,mixed',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'IBAN' => 'required|string|max:50',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'fishing_license_number' => 'required|string|max:50',
            'fishing_license_expiry' => 'required|date',
        ];

        if ($this->nationality === 'سعودي') {
            $rules['id_number'] = ['required', 'string', 'max:50', Rule::unique('users', 'id_number')->when($crew, fn ($rule) => $rule->ignore($crew->id))];
            $rules['id_attachment'] = [
                $crew && $crew->id_attachment ? 'nullable' : 'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:4096',
            ];
        } else {
            $rules['residence_number'] = 'required|string|max:50';
            $rules['attachment'] = [
                $crew && $crew->attachment ? 'nullable' : 'required',
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

    public function messages(): array
    {
        return [
            'id_number.unique' => 'رقم الهوية موجود بالفعل',
        ];
    }
}
