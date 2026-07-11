<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Submission;
use App\Services\WorkflowService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function __construct(
        protected WorkflowService $workflowService
    ) {}

    /**
     * Daftar pengajuan yang menunggu persetujuan user (berdasarkan role).
     *
     * GET /api/approvals/pending?per_page=15
     */
    public function pending(Request $request): JsonResponse
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
            return response()->json([
                'success' => false,
                'message' => 'Role Anda tidak memiliki hak akses untuk approval.',
            ], 403);
        }

        $perPage = min($request->integer('per_page', 15), 50);

        $submissions = Submission::with(['user', 'category', 'approvals.user'])
            ->where('status', $targetStatus)
            ->latest('id')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $submissions->through(fn ($s) => [
                'id'                => $s->id,
                'submission_number' => $s->submission_number,
                'submission_date'   => $s->submission_date?->format('Y-m-d'),
                'user'              => [
                    'id'   => $s->user?->id,
                    'name' => $s->user?->name,
                ],
                'category'          => $s->category?->name,
                'amount'            => (float) $s->amount,
                'amount_formatted'  => 'Rp ' . number_format($s->amount, 0, ',', '.'),
                'description'       => $s->description,
                'status'            => $s->status,
                'status_label'      => $s->statusLabel,
                'has_attachment'    => !empty($s->attachment),
                'created_at'        => $s->created_at?->toISOString(),
                'approval_history'  => $s->approvals->map(fn ($a) => [
                    'role'        => $a->role,
                    'status'      => $a->status,
                    'notes'       => $a->notes,
                    'approved_at' => $a->approved_at?->toISOString(),
                    'user_name'   => $a->user?->name,
                ])->toArray(),
            ]),
            'meta' => [
                'current_page' => $submissions->currentPage(),
                'last_page'    => $submissions->lastPage(),
                'per_page'     => $submissions->perPage(),
                'total'        => $submissions->total(),
                'your_role'    => $role,
            ],
        ]);
    }

    /**
     * Proses persetujuan: Approve atau Reject pengajuan.
     *
     * POST /api/approvals/{submission}/process
     * Body: { "action": "approve|reject", "notes": "...", "payment_method": "...", "reference_number": "..." }
     */
    public function process(Request $request, Submission $submission): JsonResponse
    {
        $validated = $request->validate([
            'action'           => ['required', 'in:approve,reject'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'payment_method'   => ['required_if:action,approve', 'nullable', 'string', 'max:100'],
            'reference_number' => ['nullable', 'string', 'max:100'],
        ]);

        $user = $request->user();
        $role = $user->roleSlug;

        try {
            // Khusus Finance yang approve → proses pembayaran juga
            if ($role === 'finance' && $validated['action'] === 'approve') {
                return $this->processFinancePayment($submission, $user, $validated);
            }

            $this->workflowService->processApproval(
                $submission,
                $validated['action'],
                $validated['notes'] ?? null,
                $user->id,
                $role
            );

            $submission->refresh()->load(['category', 'approvals.user']);

            return response()->json([
                'success' => true,
                'message' => $validated['action'] === 'approve'
                    ? 'Pengajuan berhasil disetujui.'
                    : 'Pengajuan ditolak.',
                'data'    => [
                    'id'           => $submission->id,
                    'status'       => $submission->status,
                    'status_label' => $submission->statusLabel,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Proses pembayaran oleh Finance (approve + create payment record).
     */
    private function processFinancePayment(Submission $submission, $user, array $validated): JsonResponse
    {
        if ($submission->payment()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran untuk pengajuan ini sudah pernah diproses.',
            ], 409);
        }

        try {
            DB::transaction(function () use ($submission, $user, $validated) {
                // Cek ketersediaan anggaran dengan pessimistic locking
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
                        "Sisa: Rp " . number_format($remainingBudget, 0, ',', '.') . ", " .
                        "Pengajuan: Rp " . number_format($submission->amount, 0, ',', '.') . ", " .
                        "Kurang: Rp " . number_format($shortfall, 0, ',', '.')
                    );
                }

                // Buat record pembayaran
                $submission->payment()->create([
                    'user_id'          => $user->id,
                    'amount'           => $submission->amount,
                    'payment_date'     => now(),
                    'payment_method'   => $validated['payment_method'],
                    'reference_number' => $validated['reference_number'] ?? null,
                    'notes'            => $validated['notes'] ?? null,
                ]);

                // Update anggaran terpakai
                $budget->update([
                    'used_budget' => $budget->used_budget + $submission->amount,
                ]);

                // Proses workflow approval
                $this->workflowService->processApproval(
                    $submission,
                    'approve',
                    $validated['notes'] ?? null,
                    $user->id,
                    'finance'
                );
            });

            $submission->refresh()->load(['category', 'approvals.user', 'payment']);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses.',
                'data'    => [
                    'id'           => $submission->id,
                    'status'       => $submission->status,
                    'status_label' => $submission->statusLabel,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
