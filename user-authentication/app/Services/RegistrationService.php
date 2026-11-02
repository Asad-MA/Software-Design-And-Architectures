<?php

namespace App\Services;

final class RegistrationService
{
    public function registerUser(array $data): array
    {
        // Here you would typically save the user to the database.
        // For demonstration purposes, we'll just return a success message.

        return [
            'message' => 'User registered successfully',
            'user' => [
                'email' => $data['email'],
                'name' => $data['name'],
            ],
        ];
    }
}