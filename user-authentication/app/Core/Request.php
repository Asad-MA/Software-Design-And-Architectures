<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Immutable-ish wrapper around PHP's request globals.
 */
final class Request
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     * @param array<string, mixed> $server
     */
    public function __construct(
        private readonly array $query,
        private readonly array $body,
        private readonly array $server,
    ) {
    }

    public static function capture(): self
    {
        return new self($_GET, $_POST, $_SERVER);
    }

    public function method(): string
    {
        $method = strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET'));

        // Allow HTML forms to tunnel PUT/PATCH/DELETE via a hidden _method field
        if ($method === 'POST' && isset($this->body['_method'])) {
            $override = strtoupper((string) $this->body['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                return $override;
            }
        }

        return $method;
    }

    /**
     * Folder the app is served from, e.g. "/my-project/public" when running
     * under http://localhost/my-project/public/. Empty string at the web root.
     */
    public function basePath(): string
    {
        $dir = str_replace('\\', '/', dirname((string) ($this->server['SCRIPT_NAME'] ?? '')));

        return ($dir === '/' || $dir === '.') ? '' : rtrim($dir, '/');
    }

    /** Request path relative to the app's base path (always starts with "/"). */
    public function path(): string
    {
        $uri = (string) ($this->server['REQUEST_URI'] ?? '/');
        $path = rawurldecode(parse_url($uri, PHP_URL_PATH) ?: '/');

        $base = $this->basePath();
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        $path = '/' . trim($path, '/');

        return $path;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function parseBody(): array
    {
        if ($this->isJson()) {
            $input = file_get_contents('php://input');
            return json_decode($input ?: '', true) ?? [];
        }

        return $this->body;
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return $this->query + $this->body;
    }

    public function isJson(): bool
    {
        return str_contains((string) ($this->server['CONTENT_TYPE'] ?? ''), 'application/json');
    }
}
