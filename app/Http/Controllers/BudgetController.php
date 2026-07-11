<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $year = $request->query('year', date('Y'));
        
        $allCategories = Category::orderBy('name')->get();
        
        $budgetsForYear = Budget::with('category')
            ->where('year', $year)
            ->get()
            ->keyBy('category_id');
        
        $budgetsByCategory = collect();
        
        foreach ($allCategories as $category) {
            if (isset($budgetsForYear[$category->id])) {
                $budgetsByCategory[$category->name] = collect([$budgetsForYear[$category->id]]);
            } else {
                $emptyBudget = new Budget([
                    'category_id' => $category->id,
                    'year' => $year,
                    'total_budget' => 0,
                    'used_budget' => 0,
                ]);
                $emptyBudget->category = $category;
                $budgetsByCategory[$category->name] = collect([$emptyBudget]);
            }
        }
        
        return view('budgets.index', [
            'budgetsByCategory' => $budgetsByCategory,
            'selectedYear' => $year,
        ]);
    }

    public function getBudgetInfo(Request $request)
    {
        $categoryId = $request->query('category_id');
        $year = $request->query('year', date('Y'));

        $budget = Budget::where('category_id', $categoryId)
            ->where('year', $year)
            ->with('category')
            ->first();

        if ($budget) {
            return response()->json([
                'exists' => true,
                'total_budget' => $budget->total_budget,
                'used_budget' => $budget->used_budget,
                'remaining_budget' => $budget->remaining_budget,
                'category_name' => $budget->category->name,
            ]);
        }

        return response()->json([
            'exists' => false,
            'total_budget' => 0,
            'used_budget' => 0,
            'remaining_budget' => 0,
            'category_name' => null,
        ]);
    }

    public function store(StoreBudgetRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $existingBudget = Budget::where('category_id', $validated['category_id'])
            ->where('year', $validated['year'])
            ->first();

        if ($existingBudget) {
            $newTotal = $existingBudget->total_budget + $validated['total_budget'];
            $existingBudget->update([
                'total_budget' => $newTotal,
            ]);
            $message = "Anggaran ditambahkan. Total anggaran kategori sekarang: Rp " . number_format($newTotal, 0, ',', '.');
        } else {
            Budget::create([
                'category_id' => $validated['category_id'],
                'year' => $validated['year'],
                'total_budget' => $validated['total_budget'],
                'used_budget' => 0,
            ]);
            $message = "Anggaran baru berhasil ditambahkan";
        }

        return redirect()->route('budgets.index', ['year' => $validated['year']])
            ->with('success', $message);
    }
}
