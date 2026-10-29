<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $newValues = $model->getAttributes();
        unset($newValues['password'], $newValues['remember_token']);

        AuditLogger::log(
            action: 'CREATE',
            model: $model,
            oldValues: null,
            newValues: $newValues
        );
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $oldValues = [];
        $newValues = [];

        foreach ($changes as $key => $value) {
            if (in_array($key, ['password', 'remember_token'], true)) {
                continue;
            }
            $oldValues[$key] = $model->getOriginal($key);
            $newValues[$key] = $value;
        }

        $action = 'UPDATE';

        // Detect specific semantic actions
        if ($model instanceof Product && array_key_exists('stock', $changes)) {
            $action = 'STOCK CHANGE';
        } elseif ($model instanceof User && array_key_exists('role', $changes)) {
            $action = 'ROLE_CHANGE';
        } elseif ($model instanceof User && array_key_exists('is_banned', $changes)) {
            $action = $model->is_banned ? 'BAN_USER' : 'UNBAN_USER';
        }

        AuditLogger::log(
            action: $action,
            model: $model,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $oldValues = $model->getOriginal();
        unset($oldValues['password'], $oldValues['remember_token']);

        AuditLogger::log(
            action: 'DELETE',
            model: $model,
            oldValues: $oldValues,
            newValues: null
        );
    }
}
