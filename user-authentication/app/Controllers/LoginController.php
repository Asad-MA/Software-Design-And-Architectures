<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Controller;
use App\Interfaces\AuthenticatorInterface;

final class LoginController extends Controller
{
    public function __construct(View $view, private readonly AuthenticatorInterface $authenticator)
    {
        parent::__construct($view);
    }

    public function login(Request $request): Response
    {
        $credentials = $request->parseBody();

        if (!isset($credentials['email'], $credentials['password'])) {
            return $this->json(['error' => 'Missing email or password'], 400);
        }

        $user = $this->authenticator->authenticate($credentials);
        if ($user === null) {
            return $this->json(['error' => 'Invalid email or password'], 401);
        }

        return $this->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
            ],
        ]);
    }

    public function showLoginForm(): Response
    {
        return $this->render('login/index');
    }
}