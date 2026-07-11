<?php

namespace App\Repositories\Contracts;

interface SubmissionRepositoryInterface
{
    public function getForUser(int $userId, int $perPage = 10);
    public function findByIdAndUser(int $id, int $userId);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
