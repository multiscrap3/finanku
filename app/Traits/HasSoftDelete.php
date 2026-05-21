<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

trait HasSoftDelete
{
    use SoftDeletes;

    /**
     * Boot the trait
     */
    protected static function bootHasSoftDelete(): void
    {
        // Bisa ditambahkan logic tambahan saat soft delete
        static::deleting(function ($model) {
            // Log atau notifikasi sebelum soft delete
        });

        static::restoring(function ($model) {
            // Log atau notifikasi sebelum restore
        });
    }

    /**
     * Scope untuk hanya data yang di-soft delete
     */
    public function scopeOnlyTrashed($query)
    {
        return $query->whereNotNull($this->getQualifiedDeletedAtColumn());
    }

    /**
     * Scope untuk data dengan atau tanpa soft delete
     */
    public function scopeWithTrashed($query)
    {
        return $query->withTrashed();
    }

    /**
     * Force delete dengan konfirmasi
     */
    public function forceDeleteWithConfirmation(): bool
    {
        if ($this->trashed()) {
            return $this->forceDelete();
        }
        
        return false;
    }

    /**
     * Restore dengan log
     */
    public function restoreWithLog(): bool
    {
        if ($this->trashed()) {
            return $this->restore();
        }
        
        return false;
    }
}
