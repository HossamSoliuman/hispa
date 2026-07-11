<?php

namespace App\Http\Requests\Owner\Startup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContributionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $p = $this->route('project') ?? $this->route('contribution')?->project;

        return ['date' => ['required', 'date'], 'amount' => ['required', 'numeric', 'min:0.01'], 'partner_id' => ['required', Rule::exists('startup_partners', 'id')->where('owner_id', auth()->id())->where('project_id', $p?->id)], 'payment_method' => ['required', Rule::in(['cash', 'transfer', 'card'])], 'type' => ['required', Rule::in(['capital', 'reimbursement', 'extra'])], 'notes' => ['nullable', 'string']];
    }

    public function messages(): array
    {
        return ['partner_id.exists' => __('owner.startup.validation.partner_invalid')];
    }
}
