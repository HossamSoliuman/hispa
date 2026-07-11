<?php

namespace App\Http\Requests\Owner\Startup;

use App\Service\Owner\Startup\OwnershipAllocationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255'], 'type' => ['required', Rule::in(['marine', 'commercial', 'service', 'other'])], 'start_date' => ['required', 'date'], 'status' => ['required', Rule::in(['setup', 'active', 'paused', 'completed'])], 'description' => ['nullable', 'string'], 'notes' => ['nullable', 'string']];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $project = $this->route('project');

            if (! $project && $this->input('status') !== 'setup') {
                $validator->errors()->add('status', __('owner.startup.validation.new_project_setup'));

                return;
            }

            if ($project && $this->input('status') !== 'setup' && ! app(OwnershipAllocationService::class)->isComplete($project)) {
                $validator->errors()->add('status', __('owner.startup.validation.shares_incomplete_status'));
            }
        }];
    }

    public function messages(): array
    {
        return ['name.required' => __('owner.startup.validation.name_required'), 'start_date.required' => __('owner.startup.validation.date_required')];
    }
}
