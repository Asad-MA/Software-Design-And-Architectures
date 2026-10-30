<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Renders PHP templates from app/Views, optionally wrapped in a layout.
 *
 *   $view->render('home/index', ['title' => 'Hi']);               // uses default layout
 *   $view->render('auth/login', [], layout: null);                // no layout
 *
 * Inside templates use e($value) to escape output. The rendered page body
 * is available to layouts as $content.
 */
final class View
{
    public function __construct(
        private readonly string $viewsPath = BASE_PATH . '/app/Views',
        private readonly ?string $defaultLayout = 'layouts/main',
    ) {
    }

    /** @param array<string, mixed> $data */
    public function render(string $template, array $data = [], ?string $layout = '__default__'): string
    {
        $content = $this->renderFile($template, $data);

        $layout = $layout === '__default__' ? $this->defaultLayout : $layout;
        if ($layout === null) {
            return $content;
        }

        return $this->renderFile($layout, $data + ['content' => $content]);
    }

    /** @param array<string, mixed> $data */
    private function renderFile(string $template, array $data): string
    {
        $path = "{$this->viewsPath}/{$template}.php";
        if (!is_file($path)) {
            throw new RuntimeException("View [{$template}] not found at {$path}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $path;
        return (string) ob_get_clean();
    }
}
