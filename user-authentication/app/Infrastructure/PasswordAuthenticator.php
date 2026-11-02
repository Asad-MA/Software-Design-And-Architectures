<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\AuthenticatorInterface;
use App\Interfaces\UserRepoInterface;
use App\Interfaces\PasswordCredentialRepositoryInterface;
use App\Models\PasswordCredential;
use App\Interfaces\PasswordHasherInterface;
use App\Models\User;

final class PasswordAuthenticator implements AuthenticatorInterface
{
    public function __construct(private readonly UserRepoInterface $users, private readonly PasswordCredentialRepositoryInterface $passwords , private readonly PasswordHasherInterface $hasher) {}

    public function verify(array $credentials): ?User
    {
        $hash = $credentials['hash'] ?? null;
        $password = $credentials['password'] ?? null;

        if (!is_string($hash) || $hash === '' || !is_string($password) || $password === '') {
            return null;
        }

      //Need to delete this file

        if (!$passwordCredential || !$this->hasher->verifyPassword($password, $passwordCredential->getPasswordHash())) {
            return null;
        }

        return $user;
    }
}
