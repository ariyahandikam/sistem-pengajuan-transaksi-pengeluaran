<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;
use App\Models\Approval;

class SubmissionPolicy
{
    public function view(User $user, Submission $submission): bool
    {
        // Owner can view
        if ($user->id === $submission->user_id) {
            return true;
        }

        // Admin can view everything
        if ($user->roleSlug === 'admin') {
            return true;
        }

        // Director can view everything
        if ($user->roleSlug === 'direktur') {
            return true;
        }

        // For SPV, Manager, Finance: allow if the user has an approval record for this submission
        // or if they are currently assigned to approve it
        if (in_array($user->roleSlug, ['spv', 'manager', 'finance'])) {
            $hasApprovalRecord = Approval::where('submission_id', $submission->id)
                ->where('user_id', $user->id)
                ->exists();

            if ($hasApprovalRecord) {
                return true;
            }

            // Also allow if it's currently at their stage
            $targetStatus = match ($user->roleSlug) {
                'spv'      => Submission::STATUS_WAITING_SPV,
                'manager'  => Submission::STATUS_WAITING_MANAGER,
                'finance'  => Submission::STATUS_WAITING_FINANCE,
                default    => null,
            };

            return $submission->status === $targetStatus;
        }

        return false;
    }

    public function viewSubmission(User $user, Submission $submission): bool
    {
        return $this->view($user, $submission);
    }

    public function update(User $user, Submission $submission): bool
    {
        return $user->id === $submission->user_id && $submission->isEditable();
    }

    public function delete(User $user, Submission $submission): bool
    {
        return $user->id === $submission->user_id && $submission->isEditable();
    }
}
