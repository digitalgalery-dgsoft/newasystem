<?php

/*
|--------------------------------------------------------------------------
| Octane Configuration (Safe Fallback)
|--------------------------------------------------------------------------
| Provides safe defaults without crashing when Laravel Octane is not installed.
*/

if (!class_exists('Laravel\Octane\Octane')) {
    return [
        'server' => env('OCTANE_SERVER', 'roadrunner'),
        'https' => env('OCTANE_HTTPS', false),
        'listeners' => [],
        'warm' => [],
        'flush' => [],
        'garbage' => 50,
        'max_execution_time' => 30,
    ];
}

return [
    'server' => env('OCTANE_SERVER', 'roadrunner'),
    'https' => env('OCTANE_HTTPS', false),
    'listeners' => class_exists('Laravel\Octane\Octane') ? \Laravel\Octane\Octane::defaultListeners() : [],
    'warm' => [],
    'flush' => [],
    'garbage' => 50,
    'max_execution_time' => 30,
];
