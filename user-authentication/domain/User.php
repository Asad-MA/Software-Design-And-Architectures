<?php
final class User {
    public function __construct(public string $id, public string $email, public string $displayName)
    {
        
    }

    public function isActive(): bool 
    {
        return true;
    }
}