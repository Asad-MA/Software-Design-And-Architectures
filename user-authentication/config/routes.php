<?php

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Core\Router;

/**
 * Register all routes here. Add new features by adding a controller
 * and a line or two below; nothing else in the core needs to change.
 */
return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/login', [LoginController::class, 'showLoginForm']);
    $router->post('/login', [LoginController::class, 'login']);
    $router->get('/register', [\App\Controllers\RegisterController::class, 'showRegistrationForm']);
    $router->post('/register', [\App\Controllers\RegisterController::class, 'register']);
    $router->get('/health', [HomeController::class, 'health']);
};
