<?php
namespace App\Interfaces;

interface SessionInterface
{
    public function start(): void;
    public function set(string $key, mixed $value): void;
    public function get(string $key): mixed;
    public function has(string $key): bool;
    public function remove(string $key): void;
    public function clear(): void;
    public function destroy(): void;
    public function regenerateId(): void;
}