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

    public function authenticate(array $credentials): ?User
    {
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        if (!is_string($email) || $email === '' || !is_string($password) || $password === '') {
            return null;
        }

        $user = $this->users->findByEmail($email);
        if (!$user) {
            return null;
        }

        $passwordCredential = $this->passwords->findByUserId($user->getId());
        if (!$passwordCredential || !$this->hasher->verifyPassword($password, $passwordCredential->getPasswordHash())) {
            return null;
        }

        return $user;
    }
}
