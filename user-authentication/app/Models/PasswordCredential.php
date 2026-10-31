<?php

namespace App\Models;

final class PasswordCredential
{
    public function __construct(
        private readonly string $userId,
        private readonly string $passwordHash,
    
    ) {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function generatePasswordHash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }
}
