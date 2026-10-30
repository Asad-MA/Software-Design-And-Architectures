<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

/**
 * Ties the request lifecycle together: capture -> route -> respond,
 * with a single place that turns exceptions into error pages.
 */
final class Application
{
    public function __construct(private readonly Container $container)
    {
    }

    public function run(): void
    {
        $request = Request::capture();

        try {
            $response = $this->container->get(Router::class)->dispatch($request);
        } catch (HttpException $e) {
            $response = $this->errorResponse($e->statusCode);
        } catch (Throwable $e) {
            error_log((string) $e);
            $response = $this->errorResponse(500, $e);
        }

        $response->send();
    }

    private function errorResponse(int $status, ?Throwable $e = null): Response
    {
        $debug = (bool) $this->container->get(Config::class)->get('app.debug', false);
        $view = new View();
        $template = is_file(BASE_PATH . "/app/Views/errors/{$status}.php") ? "errors/{$status}" : 'errors/error';

        $html = $view->render($template, [
            'title'   => "Error {$status}",
            'status'  => $status,
            'details' => $debug && $e !== null ? (string) $e : null,
        ]);

        return new Response($html, $status);
    }
}
