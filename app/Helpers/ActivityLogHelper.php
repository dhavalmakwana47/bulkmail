<?php

use App\Enums\ActionType;
use App\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Model;

if (!function_exists('activity_log')) {
    function activity_log(
        string $module,
        ActionType|string $action,
        ?Model $loggable = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ) {
        return app(ActivityLogService::class)->log($module, $action, $loggable, $oldValues, $newValues);
    }
}
