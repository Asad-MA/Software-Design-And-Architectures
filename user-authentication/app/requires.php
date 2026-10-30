<?php

declare(strict_types=1);

/**
 * Loads every class file the application needs, using plain require_once.
 *
 * Rules of thumb:
 *   1. A file must be loaded BEFORE anything that extends/implements it
 *      (interfaces and parent classes first).
 *   2. When you add a new class, add one line here. That's all the "wiring".
 *
 * Namespaces (App\Core, App\Models, ...) are kept only as labels that group
 * related classes; they do not load anything. `use` statements just create
 * short aliases for those names.
 */

$app = BASE_PATH . '/app';

// --- Core (framework plumbing) ---
require_once $app . '/Core/helpers.php';        // url(), e()
require_once $app . '/Core/Env.php';
require_once $app . '/Core/Config.php';
require_once $app . '/Core/Container.php';
require_once $app . '/Core/Database.php';
require_once $app . '/Core/HttpException.php';
require_once $app . '/Core/Request.php';
require_once $app . '/Core/Response.php';
require_once $app . '/Core/View.php';
require_once $app . '/Core/Middleware.php';     // interface
require_once $app . '/Core/Model.php';          // abstract
require_once $app . '/Core/Controller.php';     // abstract: before every controller
require_once $app . '/Core/Router.php';
require_once $app . '/Core/Application.php';

// --- Models ---
require_once $app . '/Models/User.php';
require_once $app . '/Models/PasswordCredential.php';

// --- Interfaces (contracts): before the classes that implement them ---
require_once $app . '/Interfaces/UserRepoInterface.php';
require_once $app . '/Interfaces/PasswordCredentialRepositoryInterface.php';
require_once $app . '/Interfaces/AuthenticatorInterface.php';

// --- Repositories (persistence) ---
require_once $app . '/Repositories/UserRepo.php';
require_once $app . '/Repositories/PasswordCredentialsRepo.php';

// --- Services (business logic) ---
require_once $app . '/Services/PasswordAuthenticator.php';

// --- Controllers (HTTP layer) ---
require_once $app . '/Controllers/HomeController.php';
require_once $app . '/Controllers/LoginController.php';
require_once $app . '/Controllers/RegisterController.php';
