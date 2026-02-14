<?php

namespace App\Services;

use App\Models\TransactionLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionLogger
{
    public function log(
        Model $entity,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        try {
            TransactionLog::create([
                'entity_type' => Str::snake(class_basename($entity)),
                'entity_id' => $entity->getKey(),
                'action' => $action,
                'old_values' => $oldValues,
                'new_values' => $newValues ?? $entity->fresh()?->toArray(),
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log transaction', [
                'entity_type' => Str::snake(class_basename($entity)),
                'entity_id' => $entity->getKey(),
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
