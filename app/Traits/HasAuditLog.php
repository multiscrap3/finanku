<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAuditLog
{
    /**
     * Boot the trait
     */
    protected static function bootHasAuditLog(): void
    {
        // Log saat created
        static::created(function ($model) {
            $model->logAudit('created', null, $model->getAuditableAttributes());
        });

        // Log saat updated
        static::updated(function ($model) {
            $model->logAudit('updated', $model->getOriginal(), $model->getChanges());
        });

        // Log saat deleted
        static::deleted(function ($model) {
            $model->logAudit('deleted', $model->getAuditableAttributes(), null);
        });

        // Log saat restored (soft delete)
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->logAudit('restored', null, $model->getAuditableAttributes());
            });
        }
    }

    /**
     * Relasi ke audit logs
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'model', 'model_type', 'model_id');
    }

    /**
     * Log audit trail
     */
    protected function logAudit(string $action, ?array $oldValues, ?array $newValues): void
    {
        if (!auth()->check() || !auth()->user()->household_id) {
            return;
        }

        AuditLog::create([
            'household_id' => auth()->user()->household_id,
            'user_id' => auth()->id(),
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'action' => $action,
            'old_values' => $oldValues ? $this->filterAuditableAttributes($oldValues) : null,
            'new_values' => $newValues ? $this->filterAuditableAttributes($newValues) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get attributes yang akan di-audit
     */
    protected function getAuditableAttributes(): array
    {
        $attributes = $this->getAttributes();
        return $this->filterAuditableAttributes($attributes);
    }

    /**
     * Filter attributes yang tidak perlu di-audit
     */
    protected function filterAuditableAttributes(array $attributes): array
    {
        $excluded = $this->auditExclude ?? ['password', 'remember_token', 'created_at', 'updated_at'];
        
        return array_diff_key($attributes, array_flip($excluded));
    }
}
