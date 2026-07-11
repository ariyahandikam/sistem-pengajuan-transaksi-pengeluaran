<?php

namespace App\Services;

use App\Helpers\SubmissionNumberHelper;
use App\Models\Submission;
use App\Repositories\Contracts\SubmissionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class SubmissionService
{
    public function __construct(
        protected SubmissionRepositoryInterface $repository,
        protected WorkflowService $workflowService
    ) {}

    /**
     * @param array $data
     * @param \Illuminate\Http\UploadedFile[]|null $files
     * @param int $userId
     * @return Submission
     */
    public function createSubmission(array $data, ?array $files, int $userId): Submission
    {
        return DB::transaction(function () use ($data, $files, $userId) {
            $data = is_array($data) ? $data : (array) $data;

            $attachmentPaths = [];
            if ($files && is_array($files)) {
                foreach ($files as $file) {
                    $attachmentPaths[] = $file->store('submissions', 'public');
                }
            }

            $submissionData = [
                'submission_number' => SubmissionNumberHelper::generate(),
                'submission_date'   => now()->toDateString(),
                'user_id'           => $userId,
                'category_id'       => $data['category_id'],
                'amount'            => $data['amount'],
                'description'       => $data['description'],
                'attachment'        => $attachmentPaths,
                'status'            => Submission::STATUS_DRAFT, 
            ];

            $submission = $this->repository->create($submissionData);
            \Log::info('SubmissionService::createSubmission', ['id' => $submission->id, 'action' => $data['action'] ?? 'unknown']);

            $action = $data['action'] ?? 'draft';
            if ($action === 'submit') {
                \Log::info('SubmissionService: calling startWorkflow', ['submission_id' => $submission->id]);
                $this->workflowService->startWorkflow($submission);
            }

            return $submission;
        });
    }

    /**
     * @param Submission $submission
     * @param array $data
     * @param \Illuminate\Http\UploadedFile[]|null $files
     * @return bool
     */
    public function updateSubmission(Submission $submission, array $data, ?array $files): bool
    {
        if (!$submission->isEditable()) {
            throw new Exception("Hanya pengajuan dengan status Draft yang bisa diedit.");
        }

        $data = is_array($data) ? $data : (array) $data;

        return DB::transaction(function () use ($submission, $data, $files) {
            $attachmentPaths = $submission->attachment ?? [];
            
            // Jika ada file baru diupload, timpa semua file lama
            if ($files && is_array($files)) {
                if (!empty($attachmentPaths) && is_array($attachmentPaths)) {
                    foreach ($attachmentPaths as $path) {
                        if (Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                }
                
                $attachmentPaths = [];
                foreach ($files as $file) {
                    $attachmentPaths[] = $file->store('submissions', 'public');
                }
            }

            $updateData = [
                'category_id' => $data['category_id'],
                'amount'      => $data['amount'],
                'description' => $data['description'],
                'attachment'  => $attachmentPaths,
            ];

            $this->repository->update($submission->id, $updateData);
            $submission->refresh();

            if (isset($data['action']) && $data['action'] === 'submit') {
                $this->workflowService->startWorkflow($submission);
            }

            return true;
        });
    }

    public function deleteSubmission(Submission $submission): bool
    {
        if (!$submission->isEditable()) {
            throw new Exception("Hanya pengajuan dengan status Draft yang bisa dihapus.");
        }

        return DB::transaction(function () use ($submission) {
            if ($submission->attachment && is_array($submission->attachment)) {
                foreach ($submission->attachment as $path) {
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }

            return $this->repository->delete($submission->id);
        });
    }
}
