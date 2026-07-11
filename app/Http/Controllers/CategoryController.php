<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Category::create([
            'name' => $validated['name'],
            'is_po_produk' => $validated['is_po_produk'] ?? false,
        ]);

        return back()->with('success', 'Kategori baru berhasil ditambahkan');
    }
}
