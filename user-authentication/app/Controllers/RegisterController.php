<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Controller;


class RegisterController extends Controller
{
    public function __construct(View $view)
    {
        parent::__construct($view);
    }

    public function showRegistrationForm(): Response
    {
        return $this->render('register/index');
    }

    public function register(Request $request): Response
    {
        $data = $request->parseBody();

        if (!isset($data['email'], $data['password'])) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        // Here you would typically save the user to the database.

        

        // For demonstration purposes, we'll just return a success message.

        return $this->json([
            'message' => 'User registered successfully',
            'user' => [
                'email' => $data['email'],
                'name' => $data['name'],
            ],
        ]);
    }
}