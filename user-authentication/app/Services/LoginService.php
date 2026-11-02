<?php
namespace App\Services;

use App\Interfaces\UserRepoInterface;
use App\Interfaces\PasswordCredentialRepositoryInterface;
use App\Interfaces\AuthenticatorInterface;

final class LoginService
{
    public function __construct(private readonly UserRepoInterface $userRepo, private readonly PasswordCredentialRepositoryInterface $passwordCredentialRepo, private readonly AuthenticatorInterface $authenticator)
    {
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            return false;
        }

        $credentials = $this->passwordCredentialRepo->findByUserId($user->getId());

        if (!$credentials || !$this->authenticator->verify(['password' => $password, 'hash' => $credentials->getPasswordHash()])) {
            return false;
        }

        // Authentication successful, you can set session or token here if needed.
        return true;
    }
}