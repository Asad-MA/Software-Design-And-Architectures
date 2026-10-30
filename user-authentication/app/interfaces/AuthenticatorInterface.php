<?php

namespace App\Interfaces;

use App\Models\User;

interface AuthenticatorInterface
{
    public function authenticate(array $credentials): ? User;
}