<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tabungan extends Model
{
    use HasFactory, BelongsToHousehold, HasSoftDelete;

    protected $table = 'tabungan';

    protected $fillable = [
        'household_id',
        'nama',
        'deskripsi',
        'target_jumlah',
        'terkumpul',
        'target_tanggal',
        'icon',
        'warna',
        'status',
    ];

    protected $casts = [
        'target_jumlah' => 'decimal:2',
        'terkumpul' => 'decimal:2',
        'target_tanggal' => 'date',
    ];

    /**
     * Relasi ke Tabungan Transaksi
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(TabunganTransaksi::class);
    }

    /**
     * Hitung persentase tercapai
     */
    public function getPersentaseTercapaiAttribute(): float
    {
        if ($this->target_jumlah == 0) {
            return 0;
        }
        
        return ($this->terkumpul / $this->target_jumlah) * 100;
    }

    /**
     * Hitung sisa target
     */
    public function getSisaTargetAttribute(): float
    {
        return max(0, $this->target_jumlah - $this->terkumpul);
    }

    /**
     * Scope untuk tabungan aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
