<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Services\DashboardChartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardChartServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_includes_newly_added_categories_in_expense_report(): void
    {
        Category::create([
            'name' => 'Training & Development',
            'is_po_produk' => false,
        ]);

        $service = new DashboardChartService();
        $report = $service->getExpenseReport();

        $this->assertArrayHasKey('Training & Development', $report['report']);
        $this->assertEquals(0, $report['report']['Training & Development']['count']);
        $this->assertEquals(0.0, $report['report']['Training & Development']['total']);
    }
}
