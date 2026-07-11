<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\SubmissionApproved;
use App\Services\WorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_po_submission_above_5m_stops_at_manager(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Operasional',
            'is_po_produk' => false,
        ]);

        $submission = Submission::create([
            'submission_number' => 'TRX-20260708-0001',
            'submission_date' => now()->toDateString(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 6000000,
            'description' => 'Pengujian workflow',
            'status' => Submission::STATUS_DRAFT,
        ]);

        $service = new WorkflowService();
        $service->startWorkflow($submission);
        $this->assertSame(Submission::STATUS_WAITING_SPV, $submission->fresh()->status);

        $service->processApproval($submission->fresh(), 'approve', null, $user->id, 'spv');
        $this->assertSame(Submission::STATUS_WAITING_MANAGER, $submission->fresh()->status);

        $service->processApproval($submission->fresh(), 'approve', null, $user->id, 'manager');
        $this->assertSame(Submission::STATUS_WAITING_FINANCE, $submission->fresh()->status);
    }

    public function test_non_po_submission_above_10m_goes_to_director_after_manager(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Operasional',
            'is_po_produk' => false,
        ]);

        $submission = Submission::create([
            'submission_number' => 'TRX-20260708-0002',
            'submission_date' => now()->toDateString(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 11000000,
            'description' => 'Pengujian workflow',
            'status' => Submission::STATUS_DRAFT,
        ]);

        $service = new WorkflowService();
        $service->startWorkflow($submission);
        $service->processApproval($submission->fresh(), 'approve', null, $user->id, 'spv');
        $service->processApproval($submission->fresh(), 'approve', null, $user->id, 'manager');

        $this->assertSame(Submission::STATUS_WAITING_DIREKTUR, $submission->fresh()->status);
    }

    public function test_finance_approval_sends_email_to_staff(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Operasional',
            'is_po_produk' => false,
        ]);

        $submission = Submission::create([
            'submission_number' => 'TRX-20260708-0003',
            'submission_date' => now()->toDateString(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 1000000,
            'description' => 'Pengujian workflow finance',
            'status' => Submission::STATUS_WAITING_FINANCE,
        ]);

        $service = new WorkflowService();
        $service->processApproval($submission->fresh(), 'approve', null, $user->id, 'finance');

        Notification::assertSentTo($user, SubmissionApproved::class);
    }
}
