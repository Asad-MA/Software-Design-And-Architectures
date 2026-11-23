<?php 

namespace App\Services;

use App\Interfaces\PasswordHasherInterface;

final class NativePasswordHasher implements PasswordHasherInterface
{

     public function __construct(
        private readonly string|int|null $algo = PASSWORD_DEFAULT,
        private readonly array $options = [],
    ) {}

    public function hashPassword(string $password): string
    {
        return password_hash($password, $this->algo, $this->options);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, $this->algo, $this->options);
    }
}