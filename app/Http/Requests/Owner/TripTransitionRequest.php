<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TripTransitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'to' => ['required', 'integer', Rule::in([2, 3, 4, 5, 6, 7, 8])],
            'cancel_reason' => ['nullable', 'string', 'required_if:to,3'],
            'actual_end_date' => ['nullable', 'date_format:Y-m-d\TH:i'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'to.required' => __('trips.errors.invalid_transition'),
            'to.in' => __('trips.errors.invalid_transition'),
            'cancel_reason.required_if' => __('trips.errors.cancel_reason_required'),
        ];
    }
}
