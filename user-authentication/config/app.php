<?php

use App\Core\Env;

return [
    'name'     => Env::get('APP_NAME', 'PHP MVC Practice'),
    'debug'    => Env::get('APP_DEBUG', true),
    'timezone' => Env::get('APP_TIMEZONE', 'UTC'),
];
