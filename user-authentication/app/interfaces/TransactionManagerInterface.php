<?php

namespace App\Interfaces;

interface TransactionManagerInterface
{
    /**
     * @template T
     * @param callable():T $callback
     * @return T
     */
    public function run(callable $callback): mixed;
}