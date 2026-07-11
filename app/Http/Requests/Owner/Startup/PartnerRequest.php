<?php

namespace App\Http\Requests\Owner\Startup;

use App\Service\Owner\Startup\OwnershipAllocationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255'], 'share_percent' => ['required', 'numeric', 'decimal:0,2', 'gt:0', 'lte:100'], 'phone' => ['nullable', 'string', 'max:50'], 'partner_type' => ['required', Rule::in(['owner', 'investor', 'manager'])], 'has_salary' => ['nullable', 'boolean'], 'notes' => ['nullable', 'string']];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $project = $this->route('project') ?? $this->route('partner')?->project;
            if (! $project) {
                return;
            }

            $total = app(OwnershipAllocationService::class)->totalBasisPoints(
                $project,
                $this->route('partner'),
                $this->input('share_percent'),
            );

            if ($total > 10000) {
                $validator->errors()->add('share_percent', __('owner.startup.validation.shares_exceed'));
            }
        }];
    }

    public function messages(): array
    {
        return ['name.required' => __('owner.startup.validation.name_required')];
    }
}
