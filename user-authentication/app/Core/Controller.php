<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base controller with small response helpers.
 * Controllers receive their dependencies through the constructor (autowired).
 */
abstract class Controller
{
    public function __construct(protected readonly View $view)
    {
    }

    /** @param array<string, mixed> $data */
    protected function render(string $template, array $data = [], int $status = 200): Response
    {
        return new Response($this->view->render($template, $data), $status);
    }

    /** @param array<string, mixed>|object $data */
    protected function json(array|object $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $url): Response
    {
        return Response::redirect($url);
    }
}
