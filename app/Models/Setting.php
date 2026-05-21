<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Relasi ke Household
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * Get value dengan casting sesuai type
     */
    public function getValueAttribute($value)
    {
        return match($this->type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Set value dengan encoding sesuai type
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = match($this->type) {
            'boolean' => $value ? 'true' : 'false',
            'json' => json_encode($value),
            default => $value,
        };
    }

    /**
     * Scope untuk global settings
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('household_id');
    }

    /**
     * Scope untuk household settings
     */
    public function scopeForHousehold($query, int $householdId)
    {
        return $query->where('household_id', $householdId);
    }
}
