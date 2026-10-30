<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\AuthenticatorInterface;
use App\Interfaces\UserRepoInterface;
use App\Interfaces\PasswordCredentialRepositoryInterface;
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

        $passwordHash = $this->passwords->findByUserId($record['id']);

        print_r($passwordHash);

        // if (!password_verify($password, $record['password_hash'])) {
        //     return null;
        // }

        return new User(
            id: (string) $record['id'],
            email: (string) $record['email'],
            name: (string) ($record['name'] ?? $record['email'])
        );
    }
}
