<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\AuthenticatorInterface;
use App\Interfaces\UserRepoInterface;
use App\Interfaces\PasswordCredentialRepositoryInterface;
use App\Models\PasswordCredential;
use App\Models\User;

final class PasswordAuthenticator implements AuthenticatorInterface
{
    public function __construct(private readonly UserRepoInterface $users , private readonly PasswordCredentialRepositoryInterface $passwords)
    {
    }

    public function authenticate(array $credentials): ?User
    {
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        if (!is_string($email) || $email === '' || !is_string($password) || $password === '') {
            return null;
        }

        $record = $this->users->findByEmail($email);
        if ($record === null || !isset($record['id']) || !is_string($record['id'])) {
            return null;
        }

        $user = new User(
            id: (string) $record['id'],
            email: (string) $record['email'],
            name: (string) ($record['name'] ?? $record['email'])
        );

        $passwordHash = $this->passwords->findByUserId($record['id']);

        $passwordCredential = new PasswordCredential(
            userId: $user->getId(),
            passwordHash: $passwordHash ?? ''
        );

        if (!$passwordCredential->verifyPassword($password)) {
            return null;
        }

        return $user;
    }

   
}
