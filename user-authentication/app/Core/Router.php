<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use RuntimeException;

/**
 * Maps METHOD + path pattern to [Controller::class, 'method'] (or a closure).
 * Path params use {name} and are passed to the action as arguments.
 * Middleware are class names implementing App\Core\Middleware.
 *
 *   $router->get('/users/{id}', [UserController::class, 'show'], [AuthMiddleware::class]);
 */
final class Router
{
    /** @var array<int, array{method: string, regex: string, handler: array|Closure, middleware: array<int, class-string<Middleware>>}> */
    private array $routes = [];

    public function __construct(private readonly Container $container)
    {
    }

    /** @param array<int, class-string<Middleware>> $middleware */
    public function get(string $path, array|Closure $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    /** @param array<int, class-string<Middleware>> $middleware */
    public function post(string $path, array|Closure $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    /** @param array<int, class-string<Middleware>> $middleware */
    public function put(string $path, array|Closure $handler, array $middleware = []): void
    {
        $this->add('PUT', $path, $handler, $middleware);
    }

    /** @param array<int, class-string<Middleware>> $middleware */
    public function delete(string $path, array|Closure $handler, array $middleware = []): void
    {
        $this->add('DELETE', $path, $handler, $middleware);
    }

    /** @param array<int, class-string<Middleware>> $middleware */
    public function add(string $method, string $path, array|Closure $handler, array $middleware = []): void
    {
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', rtrim($path, '/') ?: '/');
        $this->routes[] = [
            'method'     => $method,
            'regex'      => '#^' . $regex . '$#',
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $pathMatched = false;

        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $request->path(), $matches)) {
                continue;
            }

            $pathMatched = true;
            if ($route['method'] !== $request->method()) {
                continue;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $core = fn (Request $req): Response => $this->invoke($route['handler'], $req, $params);

            return $this->runMiddleware($route['middleware'], $request, $core);
        }

        throw new HttpException($pathMatched ? 405 : 404);
    }

    /**
     * @param array<int, class-string<Middleware>> $stack
     */
    private function runMiddleware(array $stack, Request $request, Closure $core): Response
    {
        $pipeline = array_reduce(
            array_reverse($stack),
            fn (Closure $next, string $class): Closure =>
                fn (Request $req): Response => $this->container->get($class)->handle($req, $next),
            $core,
        );

        return $pipeline($request);
    }

    /**
     * @param array<string, string> $params
     */
    private function invoke(array|Closure $handler, Request $request, array $params): Response
    {
        if ($handler instanceof Closure) {
            $result = $handler($request, ...array_values($params));
        } else {
            [$class, $method] = $handler;
            $controller = $this->container->get($class);
            $result = $controller->$method($request, ...array_values($params));
        }

        if (!$result instanceof Response) {
            throw new RuntimeException('Route handlers must return an App\Core\Response.');
        }

        return $result;
    }
}
