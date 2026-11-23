<?php

declare(strict_types=1);

if (!function_exists('url')) {
    /** Build an app URL that works from any subfolder: url('/health'). */
    function url(string $path = '/'): string
    {
        $dir = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '')));
        $base = ($dir === '/' || $dir === '.') ? '' : rtrim($dir, '/');

        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('e')) {
    /** Escape a value for safe HTML output (XSS protection). */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
