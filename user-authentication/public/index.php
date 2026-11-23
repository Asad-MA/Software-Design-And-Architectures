<?php

declare(strict_types=1);

/**
 * Front controller: every request enters here.
 * Keep this file tiny; all wiring lives in bootstrap.php.
 */

$app = require dirname(__DIR__) . '/bootstrap.php';

$app->run();
