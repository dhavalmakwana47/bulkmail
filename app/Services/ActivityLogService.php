<?php

namespace App\Services;

use App\Enums\ActionType;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    public function log(
        string $module,
        ActionType|string $action,
        ?Model $loggable = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ActivityLog {
        return ActivityLog::create([
            'module' => $module,
            'action' => $action instanceof ActionType ? $action->value : $action,
            'user_id' => Auth::id(),
            'loggable_type' => $loggable?->getMorphClass(),
            'loggable_id' => $loggable?->getKey(),
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => request()->method(),
            'url' => request()->fullUrl(),
        ]);
    }

    public function logCreate(string $module, Model $model): ActivityLog
    {
        return $this->log($module, ActionType::CREATE, $model, null, $this->sanitizeAttributes($model));
    }

    public function logUpdate(string $module, Model $model, array $oldValues): ActivityLog
    {
        $sanitizedOld = $this->sanitizeArray($oldValues);
        $sanitizedNew = $this->sanitizeAttributes($model);
        
        return $this->log($module, ActionType::UPDATE, $model, $sanitizedOld, $sanitizedNew);
    }

    public function logDelete(string $module, Model $model): ActivityLog
    {
        return $this->log($module, ActionType::DELETE, $model, $this->sanitizeAttributes($model), null);
    }

    private function sanitizeAttributes(Model $model): array
    {
        return $this->sanitizeArray($model->getAttributes());
    }

    private function sanitizeArray(?array $data): ?array
    {
        if (is_null($data)) {
            return null;
        }
        
        // Remove sensitive fields
        unset($data['password'], $data['remember_token']);
        
        $result = [];
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $result[$key] = null;
            } elseif ($value instanceof \BackedEnum) {
                $result[$key] = $value->value;
            } elseif ($value instanceof \UnitEnum) {
                $result[$key] = $value->name;
            } elseif ($value instanceof \Illuminate\Support\Carbon) {
                $result[$key] = $value->toDateTimeString();
            } elseif ($value instanceof \DateTimeInterface) {
                $result[$key] = $value->format('Y-m-d H:i:s');
            } elseif (is_array($value)) {
                $result[$key] = json_encode($value);
            } elseif (is_object($value)) {
                $result[$key] = null;
            } elseif (is_scalar($value)) {
                $result[$key] = $value;
            } else {
                $result[$key] = null;
            }
        }
        
        return $result;
    }
}
