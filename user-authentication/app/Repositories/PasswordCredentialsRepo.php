<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\PasswordCredential;
use App\Interfaces\PasswordCredentialRepositoryInterface;

final class PasswordCredentialsRepo implements PasswordCredentialRepositoryInterface
{
    public function __construct(private readonly Database $db)
    {
    }

    public function findByUserId(string $userId): ?PasswordCredential
    {
        $row = $this->db->fetchOne(
            'SELECT user_id, password_hash FROM password_credentials WHERE user_id = :user_id LIMIT 1',
            ['user_id' => $userId]
        );

        if ($row) {
            return new PasswordCredential(
                userId: (string) $row['user_id'],
                passwordHash: (string) $row['password_hash']
            );
        }

        return null;
    }

    public function save(PasswordCredential $credential): void
    {
        $this->db->execute(
            'INSERT INTO password_credentials (user_id, password_hash) VALUES (:user_id, :password_hash)',
            [
                'user_id' => $credential->getUserId(),
                'password_hash' => $credential->getPasswordHash(),
               
            ]
        );
    }
}