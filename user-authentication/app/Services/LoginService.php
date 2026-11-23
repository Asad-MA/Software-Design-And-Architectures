<?php
namespace App\Services;

use App\Interfaces\UserRepoInterface;
use App\Interfaces\PasswordCredentialRepositoryInterface;
use App\Interfaces\PasswordHasherInterface;
use App\Models\User;

final class LoginService
{
    public function __construct(private readonly UserRepoInterface $userRepo, private readonly PasswordCredentialRepositoryInterface $passwordCredentialRepo, private readonly PasswordHasherInterface $passwordHasher)
    {
    }

    public function login(string $email, string $password): User|null
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            return null;
        }

        $credentials = $this->passwordCredentialRepo->findByUserId($user->getId());

        if (!$credentials || !$this->passwordHasher->verifyPassword($password, $credentials->getPasswordHash())) {
            return null;
        }

        // Authentication successful, you can set session or token here if needed.
        return $user;
    }
}