<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('staff');
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string',
            'attachment'   => 'nullable|array|max:5',
            'attachment.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'action'      => 'required|in:draft,submit',
        ];
    }
}
