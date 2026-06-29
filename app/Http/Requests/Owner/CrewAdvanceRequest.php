<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrewAdvanceRequest extends FormRequest
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
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->where('owner_id', auth()->id())
                    ->whereIn('role', ['crew', 'captain', 'employee']),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => __('owner.crew_advances.errors.person_required'),
            'user_id.exists' => __('owner.crew_advances.errors.person_invalid'),
            'amount.required' => __('owner.crew_advances.errors.amount_required'),
            'amount.min' => __('owner.crew_advances.errors.amount_min'),
            'date.required' => __('owner.crew_advances.errors.date_required'),
        ];
    }
}
