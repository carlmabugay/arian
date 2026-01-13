<?php

namespace App\Filament\Traits;

use App\Enums\SystemAction;
use App\Loggers\AuditLogger;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($model) => AuditLogger::log(SystemAction::Create, $model, null, $model->getAttributes())
        );

        static::updating(function ($model) {
            $model->auditOld = $model->getOriginal();
        });

        static::updated(fn ($model) => AuditLogger::log(
            SystemAction::Update,
            $model,
            $model->auditOld ?? null,
            $model->getChanges()
        )
        );

        static::deleted(fn ($model) => AuditLogger::log(SystemAction::Delete, $model, $model->getOriginal())
        );

        static::restored(fn ($model) => AuditLogger::log(SystemAction::Restore, $model)
        );

        static::forceDeleted(fn ($model) => AuditLogger::log(SystemAction::ForceDelete, $model)
        );
    }
}
