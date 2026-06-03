<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
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
        $id = $this->route('employee');
        $employee = \App\Models\User::find($id);

        $rules = [
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->when($employee, fn ($rule) => $rule->ignore($employee->id))],
            'name' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->when($employee, fn ($rule) => $rule->ignore($employee->id))],
            'job_title' => 'required|string|max:255',
            'salary_amount' => 'required|numeric|min:0',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'IBAN' => 'required|string|max:50',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:0,1',
        ];

        if ($this->nationality === 'سعودي') {
            $rules['id_number'] = ['required', 'string', 'max:50', Rule::unique('users', 'id_number')->when($employee, fn ($rule) => $rule->ignore($employee->id)),
            ];
        } else {
            $rules['residence_number'] = 'required|string|max:50';
            $rules['passport_number'] = 'required|string|max:50';
            $rules['residence_start_date'] = 'required|date';
            $rules['residence_end_date'] = 'required|date|after_or_equal:residence_start_date';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'id_number.unique' => 'رقم الهوية موجود بالفعل',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => $this->status == 1 ? 1 : 0,
        ]);
    }
}
