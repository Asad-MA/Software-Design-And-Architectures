<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\UserRepoInterface;
use App\Models\User;

final class UserRepo implements UserRepoInterface
{
    public function __construct(private readonly Database $db)
    {
    }

    /** @return array<string, mixed>|null */
    public function findByEmail(string $email): ?User
    {
        $record  = $this->db->fetchOne(
            'SELECT * FROM users WHERE email = :email LIMIT 1',
            ['email' => $email]
        );

        return $record ? new User(
            id: (string) $record['id'],
            email: (string) $record['email'],
            name: (string) ($record['name'] ?? $record['email'])
        ) : null;
    }

    public function create(User $user): int
    {
        $this->db->execute(
            'INSERT INTO users (email, display_name) VALUES (:email, :display_name)',
            [
                'email' => $user->getEmail(),
                'display_name' => $user->getName(),
            ]
        );

        return (int) $this->db->lastInsertId();
    }



    /** @return array<string, mixed>|null */
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM users WHERE id = :id LIMIT 1',
            ['id' => $id]
        );
    }
}