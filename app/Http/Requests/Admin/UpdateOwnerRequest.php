<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ownerId = $this->route('id');

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'status' => 'required|in:0,1',
            'owner_type' => 'required|in:fisherman,company',
        ];

        if ($this->filled('email')) {
            $rules['email'] = 'email|max:255|unique:users,email,' . $ownerId;
        }
        if ($this->filled('phone')) {
            $rules['phone'] = 'string|max:50|unique:users,phone,' . $ownerId;
        }

        // Optional: update current subscription from owner edit page
        $rules['subscription_id'] = 'nullable|exists:subscriptions,id';
        $rules['package_id'] = 'required_if:subscription_id,*|nullable|exists:subscription_packages,id';
        $rules['start_date'] = 'required_if:subscription_id,*|nullable|date';
        $rules['end_date'] = 'required_if:subscription_id,*|nullable|date|after:start_date';
        $rules['subscription_status'] = 'required_if:subscription_id,*|nullable|in:active,expired,trial,suspended';

        return $rules;
    }
}
