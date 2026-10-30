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

    
}
