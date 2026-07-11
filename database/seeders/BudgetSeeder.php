<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year;
        $categories = Category::all();

        $budgets = [
            'PO Produk'         => 50000000,
            'Operasional'       => 20000000,
            'Marketing'         => 15000000,
            'Sarana & Prasarana'=> 10000000,
            'Lain-lain'         => 5000000,
        ];

        foreach ($categories as $cat) {
            Budget::firstOrCreate(
                ['category_id' => $cat->id, 'year' => $year],
                [
                    'total_budget' => $budgets[$cat->name] ?? 5000000,
                    'used_budget'  => 0,
                ]
            );
        }
    }
}
