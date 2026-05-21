<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SumberTransaksi extends Model
{
    use HasFactory, BelongsToHousehold, HasSoftDelete;

    protected $table = 'sumber_transaksi';

    protected $fillable = [
        'household_id',
        'nama',
        'jenis',
        'nomor_rekening',
        'nama_bank',
        'saldo_awal',
        'saldo_saat_ini',
        'warna',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
        'saldo_saat_ini' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Transaksi
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    /**
     * Relasi ke Transaksi Transfer (sebagai tujuan)
     */
    public function transaksiTransferMasuk(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'transfer_ke_id');
    }

    /**
     * Update saldo
     */
    public function updateSaldo(float $amount, string $type = 'add'): void
    {
        if ($type === 'add') {
            $this->saldo_saat_ini += $amount;
        } else {
            $this->saldo_saat_ini -= $amount;
        }
        
        $this->save();
    }

    /**
     * Scope untuk sumber aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope berdasarkan jenis
     */
    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('jenis', $jenis);
    }
}
