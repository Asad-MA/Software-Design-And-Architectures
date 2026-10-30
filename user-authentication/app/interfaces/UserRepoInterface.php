<?php
namespace App\Interfaces;


interface UserRepoInterface
{
    public function findByEmail(string $email): ?array;

    public function create(string $email, string $password): int;

    public function findById(int $id): ?array;
}