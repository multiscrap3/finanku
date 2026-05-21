<?php

namespace App\Traits;

use App\Models\Household;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToHousehold
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToHousehold(): void
    {
        // Auto-assign household_id saat create
        static::creating(function ($model) {
            if (!$model->household_id && auth()->check()) {
                $model->household_id = auth()->user()->household_id;
            }
        });

        // Global scope untuk filter by household
        static::addGlobalScope('household', function (Builder $builder) {
            if (auth()->check() && auth()->user()->household_id) {
                $builder->where($builder->getModel()->getTable() . '.household_id', auth()->user()->household_id);
            }
        });
    }

    /**
     * Relasi ke Household
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * Scope untuk query tanpa filter household
     */
    public function scopeWithoutHouseholdScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('household');
    }

    /**
     * Scope untuk query household tertentu
     */
    public function scopeForHousehold(Builder $query, int $householdId): Builder
    {
        return $query->withoutGlobalScope('household')->where('household_id', $householdId);
    }
}
