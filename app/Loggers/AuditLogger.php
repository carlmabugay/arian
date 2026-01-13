<?php

namespace App\Loggers;

use App\Enums\SystemAction;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public static function log(
        SystemAction $action,
        Model $model,
        ?array $old = null,
        ?array $new = null,
        ?string $event = null
    ): void {
        AuditLog::create([
            'company_id' => auth()->user()?->company_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
