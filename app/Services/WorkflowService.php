<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\SubmissionApproved;
use App\Notifications\SubmissionForApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Exception;

class WorkflowService
{
    public function startWorkflow(Submission $submission): void
    {
        $nextStatus = null;

        DB::transaction(function () use ($submission, &$nextStatus) {
            $nextStatus = $this->determineNextStatus($submission, null);
            \Log::info('WorkflowService::startWorkflow', ['submission_id' => $submission->id, 'nextStatus' => $nextStatus]);
            $submission->update([
                'status' => $nextStatus
            ]);
        });

        if ($nextStatus) {
            \Log::info('WorkflowService: calling notifyRoleForStatus', ['submission_id' => $submission->id, 'nextStatus' => $nextStatus]);
            $this->notifyRoleForStatus($submission, $nextStatus);
        }
    }

    public function processApproval(Submission $submission, string $action, ?string $notes, int $userId, string $role): void
    {
        $this->validateApprovalRights($submission, $role);

        \Log::info('WorkflowService::processApproval START', ['submission_id' => $submission->id, 'action' => $action, 'role' => $role]);

        $nextStatus = null;  // ← DECLARE OUTSIDE TRANSACTION

        DB::transaction(function () use ($submission, $action, $notes, $userId, $role, &$nextStatus) {
            $status = $action === 'approve' ? Approval::STATUS_APPROVED : Approval::STATUS_REJECTED;
            
            $submission->approvals()->create([
                'user_id'     => $userId,
                'role'        => $role,
                'status'      => $status,
                'notes'       => $notes,
                'approved_at' => now(),
            ]);

            // Log activity for approval/reject
            activity()
                ->causedBy($userId ? \App\Models\User::find($userId) : null)
                ->performedOn($submission)
                ->withProperties(['role' => $role, 'notes' => $notes, 'module' => 'Approvals'])
                ->log($action === 'approve' ? "Approved by $role" : "Rejected by $role");

            if ($action === 'reject') {
                $submission->update(['status' => Submission::STATUS_REJECTED]);
                $nextStatus = Submission::STATUS_REJECTED;
                \Log::info('WorkflowService::processApproval REJECTED', ['submission_id' => $submission->id, 'nextStatus' => $nextStatus, 'role' => $role]);
            } else {
                $nextStatus = $this->determineNextStatus($submission, $role);
                $submission->update(['status' => $nextStatus]);
                \Log::info('WorkflowService::processApproval APPROVED', ['submission_id' => $submission->id, 'currentRole' => $role, 'nextStatus' => $nextStatus]);
            }
        });

        if ($nextStatus === Submission::STATUS_REJECTED) {
            // Send rejection email to Staff
            $staff = $submission->user;
            $roleNames = [
                'spv' => 'Supervisor',
                'manager' => 'Manager',
                'direktur' => 'Direktur',
                'finance' => 'Finance',
            ];
            $rejectedByLabel = $roleNames[$role] ?? ucfirst($role);
            
            \Log::info('WorkflowService::processApproval sending rejection email', ['submission_id' => $submission->id, 'staff_email' => $staff->email, 'rejected_by' => $rejectedByLabel]);
            Notification::send($staff, new \App\Notifications\SubmissionRejected($submission, $rejectedByLabel, $notes));
        } elseif ($nextStatus === Submission::STATUS_PAID) {
            $staff = $submission->user;
            if ($staff) {
                \Log::info('WorkflowService::processApproval sending approved email to staff', ['submission_id' => $submission->id, 'staff_email' => $staff->email]);
                Notification::send($staff, new SubmissionApproved($submission, $role));
            }
        } elseif ($nextStatus && $nextStatus !== Submission::STATUS_REJECTED) {
            \Log::info('WorkflowService::processApproval calling notifyRoleForStatus', ['submission_id' => $submission->id, 'nextStatus' => $nextStatus]);
            $this->notifyRoleForStatus($submission, $nextStatus);
        }
    }

    private function notifyRoleForStatus(Submission $submission, string $status): void
    {
        $role = $this->mapStatusToRole($status);
        \Log::info('WorkflowService::notifyRoleForStatus', ['status' => $status, 'role' => $role]);
        if (!$role) {
            \Log::warning('WorkflowService::notifyRoleForStatus: role not found for status', ['status' => $status]);
            return;
        }

        $users = User::whereHas('role', function ($q) use ($role) {
            $q->where('slug', $role);
        })->get();

        $userEmails = $users->pluck('email')->toArray();
        \Log::info('WorkflowService::notifyRoleForStatus: found users', ['role' => $role, 'count' => $users->count(), 'emails' => $userEmails]);
        if ($users->isEmpty()) {
            \Log::warning('WorkflowService::notifyRoleForStatus: no users found', ['role' => $role]);
            return;
        }

        \Log::info('WorkflowService: sending notifications', ['submission_id' => $submission->id, 'role' => $role, 'user_count' => $users->count(), 'recipient_emails' => $userEmails]);
        Notification::send($users, new SubmissionForApproval($submission, $role));
    }

    private function mapStatusToRole(string $status): ?string
    {
        return match ($status) {
            Submission::STATUS_WAITING_SPV => 'spv',
            Submission::STATUS_WAITING_MANAGER => 'manager',
            Submission::STATUS_WAITING_DIREKTUR => 'direktur',
            Submission::STATUS_WAITING_FINANCE => 'finance',
            default => null,
        };
    }

    private function determineNextStatus(Submission $submission, ?string $currentRole): string
    {
        $isPoProduk = (bool) ($submission->category->is_po_produk ?? $submission->category->name === 'PO Produk');
        $managerThreshold = config('workflow.manager_threshold', 5000000);
        $directorThreshold = config('workflow.director_threshold', 10000000);

        if ($currentRole === null) {
            if ($isPoProduk) {
                return Submission::STATUS_WAITING_DIREKTUR;
            }

            return Submission::STATUS_WAITING_SPV;
        }

        if ($currentRole === 'spv') {
            if (!$isPoProduk && $submission->amount > $managerThreshold) {
                return Submission::STATUS_WAITING_MANAGER;
            }

            return Submission::STATUS_WAITING_FINANCE;
        }

        if ($currentRole === 'manager') {
            if (!$isPoProduk && $submission->amount > $directorThreshold) {
                return Submission::STATUS_WAITING_DIREKTUR;
            }

            return Submission::STATUS_WAITING_FINANCE;
        }

        if ($currentRole === 'direktur') {
            return Submission::STATUS_WAITING_FINANCE;
        }

        if ($currentRole === 'finance') {
            return Submission::STATUS_PAID;
        }

        throw new Exception("Role $currentRole tidak valid untuk workflow.");
    }

    private function validateApprovalRights(Submission $submission, string $role): void
    {
        $validStatuses = [
            'spv'      => Submission::STATUS_WAITING_SPV,
            'manager'  => Submission::STATUS_WAITING_MANAGER,
            'direktur' => Submission::STATUS_WAITING_DIREKTUR,
            'finance'  => Submission::STATUS_WAITING_FINANCE,
        ];

        if (!isset($validStatuses[$role]) || $submission->status !== $validStatuses[$role]) {
            throw new Exception("Anda tidak memiliki hak akses atau pengajuan tidak pada tahap Anda.");
        }
    }
}
