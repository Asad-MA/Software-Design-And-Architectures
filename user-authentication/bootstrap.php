<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Config;
use App\Core\Container;
use App\Core\Database;
use App\Core\Env;
use App\Core\Router;
use App\Interfaces\AuthenticatorInterface;
use App\Interfaces\PasswordCredentialRepositoryInterface;
use App\Interfaces\UserRepoInterface;
use App\Repositories\PasswordCredentialsRepo;
use App\Repositories\UserRepo;
use App\Services\PasswordAuthenticator;

define('BASE_PATH', __DIR__);

// --- PSR-4 autoloader (works without Composer; composer.json is also provided) ---
$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require $composerAutoload;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }
        $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require $file;
        }
    });
}

// --- Environment & configuration ---
Env::load(BASE_PATH . '/.env');
$config = new Config(BASE_PATH . '/config');

// --- Error visibility driven by config ---
$debug = (bool) $config->get('app.debug', false);
ini_set('display_errors', $debug ? '1' : '0');
error_reporting(E_ALL);
date_default_timezone_set((string) $config->get('app.timezone', 'UTC'));

// --- Dependency container: register shared services here ---
$container = new Container();
$container->instance(Config::class, $config);

$container->singleton(Database::class, static fn (Container $c) => new Database(
    $c->get(Config::class)->get('database')
));

$container->singleton(
    UserRepoInterface::class,
    static fn (Container $c) => new UserRepo($c->get(Database::class))
);
$container->singleton(
    PasswordCredentialRepositoryInterface::class,
    static fn (Container $c) => new PasswordCredentialsRepo($c->get(Database::class))
);
$container->singleton(
    AuthenticatorInterface::class,
    static fn (Container $c) => new PasswordAuthenticator($c->get(UserRepoInterface::class) , $c->get(PasswordCredentialRepositoryInterface::class))
);

$container->singleton(Router::class, static function (Container $c) {
    $router = new Router($c);
    $routes = require BASE_PATH . '/config/routes.php';
    $routes($router);
    return $router;
});

return new Application($container);
