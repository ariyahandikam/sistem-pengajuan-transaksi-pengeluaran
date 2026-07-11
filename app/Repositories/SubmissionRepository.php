<?php

namespace App\Repositories;

use App\Models\Submission;
use App\Repositories\Contracts\SubmissionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SubmissionRepository implements SubmissionRepositoryInterface
{
    public function getForUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Submission::with('category')
            ->forUser($userId)
            ->latest('id')
            ->paginate($perPage);
    }

    public function findByIdAndUser(int $id, int $userId): ?Submission
    {
        return Submission::forUser($userId)->find($id);
    }

    public function create(array $data): Submission
    {
        return Submission::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $submission = Submission::find($id);
        if (!$submission) return false;
        
        return $submission->update($data);
    }

    public function delete(int $id): bool
    {
        $submission = Submission::find($id);
        if (!$submission) return false;
        
        return $submission->delete();
    }
}
