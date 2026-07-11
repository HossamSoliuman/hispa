<?php

namespace App\Http\Requests\Owner\Startup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('borne_by') !== 'partner') {
            $this->merge(['partner_id' => null]);
        }
    }

    public function rules(): array
    {
        $p = $this->route('project') ?? $this->route('loan')?->project;

        return ['name' => ['required', 'string', 'max:255'], 'lender' => ['required', 'string', 'max:255'], 'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01'], 'date' => ['required', 'date'], 'installments_count' => ['nullable', 'integer', 'min:1'], 'installment_amount' => ['nullable', 'numeric', 'decimal:0,2', 'min:0.01'], 'first_installment_date' => ['nullable', 'date'], 'borne_by' => ['required', Rule::in(['project', 'partner'])], 'partner_id' => ['nullable', 'required_if:borne_by,partner', Rule::exists('startup_partners', 'id')->where('owner_id', auth()->id())->where('project_id', $p?->id)], 'status' => ['required', Rule::in(['active', 'defaulted'])], 'notes' => ['nullable', 'string']];
    }

    public function messages(): array
    {
        return ['partner_id.required_if' => __('owner.startup.validation.partner_required')];
    }
}
