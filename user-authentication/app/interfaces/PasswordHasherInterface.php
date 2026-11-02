<?php

namespace App\Interfaces;

interface PasswordHasherInterface
{
    public function hashPassword(string $password): string;

    public function verifyPassword(string $password, string $hash): bool;

    public function needsRehash(string $hash): bool;
}