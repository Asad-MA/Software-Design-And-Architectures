<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Controller;
use App\Services\LoginService;

final class LoginController extends Controller
{
    public function __construct(View $view, private readonly LoginService $loginService)
    {
        parent::__construct($view);
    }

    public function login(Request $request): Response
    {
        $credentials = $request->parseBody();

        if (!isset($credentials['email'], $credentials['password'])) {
            return $this->json(['error' => 'Missing email or password'], 400);
        }

        $user = $this->loginService->login($credentials['email'], $credentials['password']);
        if ($user === null) {
            return $this->render('login/index', ['error' => 'Invalid email or password'], 401);
        }

        return $this->redirect(url('/dashboard'));

    }

    public function showLoginForm(): Response
    {
        return $this->render('login/index');
    }
}