<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'PO Produk',          'is_po_produk' => true],
            ['name' => 'Operasional',         'is_po_produk' => false],
            ['name' => 'Marketing',           'is_po_produk' => false],
            ['name' => 'Sarana & Prasarana',  'is_po_produk' => false],
            ['name' => 'Lain-lain',           'is_po_produk' => false],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
