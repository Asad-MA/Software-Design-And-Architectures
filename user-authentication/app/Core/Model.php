<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Optional convenience base for table-backed models/repositories.
 * Extend it, set $table, and add domain-specific methods.
 * Feel free to ignore it and write repositories on top of Database directly.
 */
abstract class Model
{
    protected string $table = '';

    public function __construct(protected readonly Database $db)
    {
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE id = ? LIMIT 1", [$id]);
    }

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }
}
