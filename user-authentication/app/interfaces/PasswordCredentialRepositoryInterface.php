<?php

namespace App\Interfaces;

use App\Models\PasswordCredential;

interface PasswordCredentialRepositoryInterface
{
    public function findByUserId(
        string $userId
    ): ?PasswordCredential;

    public function save(PasswordCredential $credential): void;
}