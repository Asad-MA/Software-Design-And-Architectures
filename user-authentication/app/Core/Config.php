<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Loads config/*.php files lazily and exposes them via dot notation:
 *   $config->get('database.host')
 */
final class Config
{
    /** @var array<string, array<string, mixed>> */
    private array $loaded = [];

    public function __construct(private readonly string $directory)
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $file = array_shift($segments);

        $value = $this->load($file);
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /** @return array<string, mixed> */
    private function load(string $file): array
    {
        if (!isset($this->loaded[$file])) {
            $path = "{$this->directory}/{$file}.php";
            $this->loaded[$file] = is_file($path) ? (array) require $path : [];
        }

        return $this->loaded[$file];
    }
}
