<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->roleSlug, ['spv', 'manager', 'direktur', 'finance']);
    }

    public function rules(): array
    {
        $rules = [
            'action' => 'required|in:approve,reject',
            'notes'  => 'nullable|string|max:1000',
        ];

        if ($this->user()->roleSlug === 'finance' && $this->input('action') === 'approve') {
            $rules['payment_method'] = 'required|string|max:255';
            $rules['reference_number'] = 'nullable|string|max:255';
        }

        return $rules;
    }
}
