<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HutangPiutang extends Model
{
    use HasFactory, BelongsToHousehold, HasSoftDelete;

    protected $table = 'hutang_piutang';

    protected $fillable = [
        'household_id',
        'jenis',
        'nama_pihak',
        'kontak',
        'jumlah_total',
        'jumlah_terbayar',
        'tanggal_mulai',
        'tanggal_jatuh_tempo',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'jumlah_total' => 'decimal:2',
        'jumlah_terbayar' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_jatuh_tempo' => 'date',
    ];

    /**
     * Relasi ke Pembayaran
     */
    public function pembayaran(): HasMany
    {
        return $this->hasMany(HutangPiutangPembayaran::class);
    }

    /**
     * Hitung sisa hutang/piutang
     */
    public function getSisaAttribute(): float
    {
        return $this->jumlah_total - $this->jumlah_terbayar;
    }

    /**
     * Hitung persentase terbayar
     */
    public function getPersentaseTerbayarAttribute(): float
    {
        if ($this->jumlah_total == 0) {
            return 0;
        }
        
        return ($this->jumlah_terbayar / $this->jumlah_total) * 100;
    }

    /**
     * Scope untuk hutang
     */
    public function scopeHutang($query)
    {
        return $query->where('jenis', 'hutang');
    }

    /**
     * Scope untuk piutang
     */
    public function scopePiutang($query)
    {
        return $query->where('jenis', 'piutang');
    }

    /**
     * Scope untuk status aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
