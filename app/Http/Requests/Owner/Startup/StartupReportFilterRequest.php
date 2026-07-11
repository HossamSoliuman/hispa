<?php

namespace App\Http\Requests\Owner\Startup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StartupReportFilterRequest extends FormRequest
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
        $project = $this->route('project');

        return [
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'category_id' => ['nullable', Rule::exists('startup_expense_categories', 'id')->where('owner_id', auth()->id())],
            'partner_id' => ['nullable', Rule::exists('startup_partners', 'id')->where('owner_id', auth()->id())->where('project_id', $project?->id)],
            'payment_method' => ['nullable', Rule::in(['cash', 'transfer', 'card'])],
            'payer_type' => ['nullable', Rule::in(['partner', 'project', 'loan'])],
            'is_shared' => ['nullable', Rule::in(['1', '0'])],
            'invoice' => ['nullable', Rule::in(['with', 'without'])],
        ];
    }
}
