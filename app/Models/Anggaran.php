<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anggaran extends Model
{
    use HasFactory, BelongsToHousehold, HasSoftDelete;

    protected $table = 'anggaran';

    protected $fillable = [
        'household_id',
        'kategori_id',
        'jumlah',
        'periode',
        'bulan',
        'tahun',
        'terpakai',
        'notifikasi_aktif',
        'threshold_notifikasi',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'terpakai' => 'decimal:2',
        'notifikasi_aktif' => 'boolean',
        'threshold_notifikasi' => 'integer',
    ];

    /**
     * Relasi ke Kategori
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Hitung persentase terpakai
     */
    public function getPersentaseTerpakaiAttribute(): float
    {
        if ($this->jumlah == 0) {
            return 0;
        }
        
        return ($this->terpakai / $this->jumlah) * 100;
    }

    /**
     * Check apakah sudah melebihi threshold
     */
    public function isOverThreshold(): bool
    {
        return $this->persentase_terpakai >= $this->threshold_notifikasi;
    }

    /**
     * Scope berdasarkan periode
     */
    public function scopeByPeriode($query, string $periode, int $tahun, ?int $bulan = null)
    {
        $query->where('periode', $periode)->where('tahun', $tahun);
        
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        
        return $query;
    }
}
