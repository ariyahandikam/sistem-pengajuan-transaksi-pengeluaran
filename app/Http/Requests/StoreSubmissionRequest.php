<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
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
            'attachment'   => 'nullable|array|max:5', // max 5 files
            'attachment.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120', // maks 5MB per file
            'action'      => 'required|in:draft,submit',
        ];
    }
}
