<?php

/*
|--------------------------------------------------------------------------
| Sanctum Configuration (Safe Guard)
|--------------------------------------------------------------------------
| Prevent fatal error when Laravel Sanctum package is not installed.
*/

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1')),
    'guard' => ['web'],
    'expiration' => null,
    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),
    'middleware' => [],
];
