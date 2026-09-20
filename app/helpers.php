<?php

use App\Services\ActivityLogger;
use App\Models\ActivityLog;

if (!function_exists('activity_log')) {
    /**
     * Helper global untuk mencatat log aktivitas.
     */
    function activity_log(
        string $action,
        string $module,
        string $description,
        mixed $subject = null,
        array $properties = []
    ): ?ActivityLog {
        return ActivityLogger::log($action, $module, $description, $subject, $properties);
    }
}
