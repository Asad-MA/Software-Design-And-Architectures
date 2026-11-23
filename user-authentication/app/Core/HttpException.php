<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/** Throw from anywhere to produce an HTTP error page (e.g. new HttpException(403)). */
final class HttpException extends RuntimeException
{
    public function __construct(public readonly int $statusCode, string $message = '')
    {
        parent::__construct($message !== '' ? $message : "HTTP {$statusCode}", $statusCode);
    }
}
