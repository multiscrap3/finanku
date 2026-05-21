<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory, BelongsToHousehold, HasSoftDelete;

    protected $table = 'kategori';

    protected $fillable = [
        'household_id',
        'nama',
        'jenis',
        'parent_id',
        'icon',
        'warna',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Relasi ke Parent Kategori
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'parent_id');
    }

    /**
     * Relasi ke Sub Kategori
     */
    public function children(): HasMany
    {
        return $this->hasMany(Kategori::class, 'parent_id');
    }

    /**
     * Relasi ke Transaksi
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    /**
     * Relasi ke Anggaran
     */
    public function anggaran(): HasMany
    {
        return $this->hasMany(Anggaran::class);
    }

    /**
     * Scope untuk kategori pemasukan
     */
    public function scopePemasukan($query)
    {
        return $query->where('jenis', 'pemasukan');
    }

    /**
     * Scope untuk kategori pengeluaran
     */
    public function scopePengeluaran($query)
    {
        return $query->where('jenis', 'pengeluaran');
    }

    /**
     * Scope untuk kategori aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk kategori parent (tanpa parent_id)
     */
    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }
}
