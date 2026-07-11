<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_director_dashboard_shows_submission_summary_cards(): void
    {
        $role = Role::create(['name' => 'Direktur', 'slug' => 'direktur']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $category = Category::create(['name' => 'Operasional', 'is_po_produk' => false]);

        Submission::create([
            'submission_number' => 'TRX-0001',
            'submission_date' => now()->toDateString(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 100000,
            'description' => 'Pengajuan 1',
            'status' => Submission::STATUS_WAITING_DIREKTUR,
        ]);
        Submission::create([
            'submission_number' => 'TRX-0002',
            'submission_date' => now()->toDateString(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 200000,
            'description' => 'Pengajuan 2',
            'status' => Submission::STATUS_REJECTED,
        ]);
        Submission::create([
            'submission_number' => 'TRX-0003',
            'submission_date' => now()->toDateString(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 300000,
            'description' => 'Pengajuan 3',
            'status' => Submission::STATUS_PAID,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertOk();
        $response->assertViewIs('dashboard.direktur');
        $response->assertViewHas('totalPengajuan', 3);
        $response->assertViewHas('menungguApproval', 1);
        $response->assertViewHas('ditolak', 1);
        $response->assertViewHas('paid', 1);
        $response->assertViewHas('submissionChart');
        $response->assertViewHas('categoryExpense');
        $response->assertViewHas('activePeriod', 'monthly');
    }

    public function test_finance_dashboard_shows_submission_summary_cards(): void
    {
        $role = Role::create(['name' => 'Finance', 'slug' => 'finance']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $category = Category::create(['name' => 'Travel', 'is_po_produk' => false]);

        Submission::create([
            'submission_number' => 'TRX-0004',
            'submission_date' => now()->toDateString(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 150000,
            'description' => 'Pengajuan 4',
            'status' => Submission::STATUS_WAITING_FINANCE,
        ]);
        Submission::create([
            'submission_number' => 'TRX-0005',
            'submission_date' => now()->toDateString(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 250000,
            'description' => 'Pengajuan 5',
            'status' => Submission::STATUS_REJECTED,
        ]);
        Submission::create([
            'submission_number' => 'TRX-0006',
            'submission_date' => now()->toDateString(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 350000,
            'description' => 'Pengajuan 6',
            'status' => Submission::STATUS_PAID,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertOk();
        $response->assertViewIs('dashboard.finance');
        $response->assertViewHas('totalPengajuan', 3);
        $response->assertViewHas('menungguApproval', 1);
        $response->assertViewHas('ditolak', 1);
        $response->assertViewHas('paid', 1);
        $response->assertViewHas('submissionChart');
        $response->assertViewHas('categoryExpense');
        $response->assertViewHas('activePeriod', 'monthly');
    }
}
