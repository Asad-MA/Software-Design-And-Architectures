<?php
namespace App\Interfaces;
use App\Models\User;

interface UserRepoInterface
{
    public function findByEmail(string $email): ?User;

    public function create(User $user): int;

    public function findById(int $id): ?array;
}