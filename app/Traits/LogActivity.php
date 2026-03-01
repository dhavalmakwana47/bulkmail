<?php

namespace App\Traits;

use App\Services\ActivityLogService;

trait LogActivity
{
    public static $disableActivityLog = false;

    protected static function bootLogActivity(): void
    {
        static::created(function ($model) {
            if (static::$disableActivityLog) return;
            
            app(ActivityLogService::class)->logCreate(
                static::getModuleName(),
                $model
            );
        });

        static::updated(function ($model) {
            if (static::$disableActivityLog) return;
            
            if ($model->wasChanged()) {
                app(ActivityLogService::class)->logUpdate(
                    static::getModuleName(),
                    $model,
                    $model->getOriginal()
                );
            }
        });

        static::deleted(function ($model) {
            if (static::$disableActivityLog) return;
            
            app(ActivityLogService::class)->logDelete(
                static::getModuleName(),
                $model
            );
        });
    }

    protected static function getModuleName(): string
    {
        return class_basename(static::class);
    }
}
