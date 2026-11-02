<?php
namespace App\Interfaces;
use App\Models\User;

interface UserRepoInterface
{
    public function findByEmail(string $email): ?User;

    public function create(string $email, string $password): int;

    public function findById(int $id): ?array;
}