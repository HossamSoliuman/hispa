<?php

namespace App\Http\Requests\Owner\Startup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class LoanPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('payer_type') !== 'partner') {
            $this->merge(['partner_id' => null]);
        }
    }

    public function rules(): array
    {
        $loan = $this->route('loan');

        return ['date' => ['required', 'date'], 'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01'], 'payer_type' => ['required', Rule::in(['project', 'partner'])], 'partner_id' => ['nullable', 'required_if:payer_type,partner', Rule::exists('startup_partners', 'id')->where('owner_id', auth()->id())->where('project_id', $loan?->project_id)], 'payment_method' => ['required', Rule::in(['cash', 'transfer', 'card'])], 'notes' => ['nullable', 'string']];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $loan = $this->route('loan');

            if (! $loan || $validator->errors()->has('amount')) {
                return;
            }

            $principal = (int) round((float) $loan->amount * 100);
            $paid = (int) round((float) $loan->payments()->sum('amount') * 100);
            $payment = (int) round((float) $this->input('amount') * 100);

            if ($payment > $principal - $paid) {
                $validator->errors()->add('amount', __('owner.startup.validation.loan_overpayment'));
            }
        }];
    }

    public function messages(): array
    {
        return ['partner_id.required_if' => __('owner.startup.validation.partner_required')];
    }
}
