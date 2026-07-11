<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('finance');
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'year' => 'required|integer|min:2020|max:2099',
            'total_budget' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori harus dipilih',
            'category_id.exists' => 'Kategori tidak ditemukan',
            'year.required' => 'Tahun harus diisi',
            'total_budget.required' => 'Total anggaran harus diisi',
            'total_budget.numeric' => 'Total anggaran harus berupa angka',
            'total_budget.min' => 'Total anggaran tidak boleh negatif',
        ];
    }
}
