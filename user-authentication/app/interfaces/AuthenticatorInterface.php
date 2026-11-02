<?php

namespace App\Interfaces;

use App\Models\User;

interface AuthenticatorInterface
{
    public function verify(array $credentials): ? User;
}