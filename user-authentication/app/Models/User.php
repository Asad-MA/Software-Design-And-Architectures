<?php

declare(strict_types=1);

namespace App\Models;

final class User
{
    public function __construct(
        private readonly string $id,
        private readonly string $email,
        private readonly string $name
    ) {
    
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return true; // Placeholder implementation
    }
}
