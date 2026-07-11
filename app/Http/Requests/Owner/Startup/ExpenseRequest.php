<?php

namespace App\Http\Requests\Owner\Startup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'partner_id' => $this->input('payer_type') === 'partner' ? $this->input('partner_id') : null,
            'loan_id' => $this->input('payer_type') === 'loan' ? $this->input('loan_id') : null,
            'is_shared' => $this->exists('is_shared') ? $this->input('is_shared') : true,
        ]);
    }

    public function rules(): array
    {
        $project = $this->route('project') ?? $this->route('expense')?->project;
        $owner = auth()->id();

        return ['date' => ['required', 'date'], 'name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01'], 'category_id' => ['required', Rule::exists('startup_expense_categories', 'id')->where('owner_id', $owner)], 'payer_type' => ['required', Rule::in(['partner', 'project', 'loan'])], 'partner_id' => ['nullable', 'required_if:payer_type,partner', Rule::exists('startup_partners', 'id')->where('owner_id', $owner)->where('project_id', $project?->id)], 'loan_id' => ['nullable', 'required_if:payer_type,loan', Rule::exists('startup_loans', 'id')->where('owner_id', $owner)->where('project_id', $project?->id)], 'payment_method' => ['required', Rule::in(['cash', 'transfer', 'card'])], 'is_shared' => ['nullable', 'boolean'], 'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'], 'remove_attachment' => ['nullable', 'boolean'], 'notes' => ['nullable', 'string']];
    }

    public function messages(): array
    {
        return ['partner_id.required_if' => __('owner.startup.validation.partner_required'), 'loan_id.required_if' => __('owner.startup.validation.loan_required')];
    }
}
