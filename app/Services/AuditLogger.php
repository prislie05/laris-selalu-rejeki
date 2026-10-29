<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Log a generic system or user action.
     *
     * @param string $action
     * @param Model|null $model
     * @param array<string, mixed>|null $oldValues
     * @param array<string, mixed>|null $newValues
     * @param int|null $customUserId
     * @param string|null $customRole
     * @return AuditLog
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $customUserId = null,
        ?string $customRole = null
    ): AuditLog {
        $user = Auth::user();
        $userId = $customUserId ?? $user?->id;
        $role = $customRole ?? $user?->role ?? 'guest';

        return AuditLog::create([
            'user_id' => $userId,
            'role' => $role,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::userAgent(),
        ]);
    }
}
