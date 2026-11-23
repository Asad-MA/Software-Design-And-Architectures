<?php

namespace App\Services;
use App\Interfaces\UserRepoInterface;
use App\Interfaces\PasswordCredentialRepositoryInterface;
use App\Interfaces\TransactionManagerInterface;
use App\Models\User;
use App\Models\PasswordCredential;
use App\Core\Database;

final class RegistrationService
{
    private readonly Database $db;

    public function __construct(private readonly UserRepoInterface $userRepo , private readonly PasswordCredentialRepositoryInterface $passwordCredentialRepo, Database $db)
    {
        $this->db = $db;
    }
 

    public function registerUser(array $data): array
    {
        return $this->db->transaction(function () use ($data) {
            $user = new User(id: '', email: $data['email'], name: $data['username']);

            $userId = $this->userRepo->create($user);

            $this->passwordCredentialRepo->save(
                new PasswordCredential(
                    userId: $userId,
                    passwordHash: password_hash($data['password'], PASSWORD_DEFAULT)
                )
            );

            return [
                'message' => 'User registered successfully',
                'user' => [
                    'email' => $user->getEmail(),
                    'name'  => $user->getName(),
                ],
            ];
        });
    }
}