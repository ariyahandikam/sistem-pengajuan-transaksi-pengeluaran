<?php

namespace App\Http\Controllers\Approver;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessApprovalRequest;
use App\Models\Budget;
use App\Models\Submission;
use App\Services\WorkflowService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function __construct(
        protected WorkflowService $workflowService
    ) {}

    public function index(Request $request): View
    {
        $role = $request->user()->roleSlug;

        $targetStatus = match ($role) {
            'spv'      => Submission::STATUS_WAITING_SPV,
            'manager'  => Submission::STATUS_WAITING_MANAGER,
            'direktur' => Submission::STATUS_WAITING_DIREKTUR,
            'finance'  => Submission::STATUS_WAITING_FINANCE,
            default    => null,
        };

        if (!$targetStatus) {
            abort(403);
        }

        $submissions = Submission::with(['user', 'category'])
            ->where('status', $targetStatus)
            ->latest('id')
            ->paginate(10);

        return view('approvals.index', compact('submissions'));
    }

    public function show(Submission $submission): View
    {
        $this->authorize('view', $submission);
        
        $submission->load(['user', 'category', 'approvals.user']);
        return view('approvals.show', compact('submission'));
    }

    public function process(ProcessApprovalRequest $request, Submission $submission): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $user = $request->user();
            
            if ($user->roleSlug === 'finance' && $validated['action'] === 'approve') {
                return $this->processFinancePayment($request, $submission, $user);
            }

            $this->workflowService->processApproval(
                $submission,
                $validated['action'],
                $validated['notes'] ?? null,
                $user->id,
                $user->roleSlug
            );

            $msg = $validated['action'] === 'approve' ? 'Pengajuan berhasil disetujui.' : 'Pengajuan ditolak.';
            return redirect()->route('approvals.index')->with('success', $msg);

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function processFinancePayment(ProcessApprovalRequest $request, Submission $submission, $user): RedirectResponse
    {
        // Check if payment already exists
        if ($submission->payment()->exists()) {
            return back()->with('error', 'Pembayaran untuk pengajuan ini sudah pernah diproses.');
        }

        try {
            DB::transaction(function () use ($request, $submission, $user) {
                // Check budget availability with pessimistic locking
                $budget = Budget::where('category_id', $submission->category_id)
                    ->where('year', $submission->submission_date->year)
                    ->lockForUpdate()
                    ->first();
    
                if (!$budget) {
                    throw new Exception("Anggaran untuk kategori '{$submission->category->name}' tahun {$submission->submission_date->year} belum ditetapkan.");
                }
    
                $remainingBudget = $budget->total_budget - $budget->used_budget;
    
                if ($submission->amount > $remainingBudget) {
                    $shortfall = $submission->amount - $remainingBudget;
                    throw new Exception(
                        "Saldo anggaran kategori '{$submission->category->name}' tidak mencukupi. " .
                        "Sisa anggaran: Rp " . number_format($remainingBudget, 0, ',', '.') . ", " .
                        "Pengajuan: Rp " . number_format($submission->amount, 0, ',', '.') . ", " .
                        "Kurang: Rp " . number_format($shortfall, 0, ',', '.')
                    );
                }
    
                $submission->payment()->create([
                    'user_id'          => $user->id,
                    'amount'           => $submission->amount,
                    'payment_date'     => now(),
                    'payment_method'   => $request->input('payment_method'),
                    'reference_number' => $request->input('reference_number'),
                    'notes'            => $request->input('notes'),
                ]);
    
                // Update used_budget saat pembayaran diproses
                $budget->update([
                    'used_budget' => $budget->used_budget + $submission->amount
                ]);
    
                $this->workflowService->processApproval(
                    $submission,
                    'approve',
                    $request->input('notes'),
                    $user->id,
                    'finance'
                );
            });
    
            return redirect()->route('approvals.index')->with('success', 'Pembayaran berhasil diproses.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
