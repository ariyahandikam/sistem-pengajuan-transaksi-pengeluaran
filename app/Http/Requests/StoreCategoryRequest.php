<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('finance');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name',
            'is_po_produk' => 'nullable|boolean',
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori harus diisi',
            'name.unique' => 'Kategori dengan nama ini sudah ada',
        ];
    }
}
