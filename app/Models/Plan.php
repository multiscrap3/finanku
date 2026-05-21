<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'slug',
        'harga',
        'max_anggota',
        'max_transaksi',
        'max_ocr',
        'fitur',
        'is_active',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'fitur' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Households
     */
    public function households(): HasMany
    {
        return $this->hasMany(Household::class);
    }

    /**
     * Relasi ke Payment History
     */
    public function paymentHistories(): HasMany
    {
        return $this->hasMany(PaymentHistory::class);
    }

    /**
     * Check apakah fitur tertentu aktif
     */
    public function hasFeature(string $feature): bool
    {
        return $this->fitur[$feature] ?? false;
    }

    /**
     * Scope untuk plan aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
