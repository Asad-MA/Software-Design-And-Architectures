<?php
namespace App\Infrastructure;

use App\Interfaces\SessionInterface;

final class NativeSession implements SessionInterface
{
    public function start(): void
    {
        if (!session_status() === PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function clear(): void
    {
        $_SESSION = [];
    }

    public function destroy(): void
    {
        session_destroy();
        $_SESSION = [];
    }

    public function regenerateId(): void
    {
        session_regenerate_id(true);
    }
}