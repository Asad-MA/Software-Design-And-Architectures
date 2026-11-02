<?php

namespace App\Infrastructure;

use App\Core\Database;
use App\Interfaces\TransactionManagerInterface;

final class PDOTransactionManager implements TransactionManagerInterface
{
    public function __construct(private readonly Database $db)
    {
    }

    /**
     * @template T
     * @param callable():T $callback
     * @return T
     */
    public function run(callable $callback): mixed
    {
        return $this->db->transaction(static fn () => $callback());
    }
}