<?php

/*
|--------------------------------------------------------------------------
| Octane Configuration (Safe Fallback)
|--------------------------------------------------------------------------
| Provides safe defaults without crashing when Laravel Octane is not installed.
*/

return [
    'server' => env('OCTANE_SERVER', 'roadrunner'),
    'https' => env('OCTANE_HTTPS', false),
    'listeners' => [],
    'warm' => [],
    'flush' => [],
    'garbage' => 50,
    'max_execution_time' => 30,
];
