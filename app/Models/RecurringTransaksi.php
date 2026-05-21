<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringTransaksi extends Model
{
    use HasFactory, BelongsToHousehold, HasSoftDelete;

    protected $table = 'recurring_transaksi';

    protected $fillable = [
        'household_id',
        'user_id',
        'kategori_id',
        'sumber_transaksi_id',
        'jenis',
        'jumlah',
        'frekuensi',
        'interval',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'next_run',
        'is_active',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'next_run' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function sumberTransaksi(): BelongsTo
    {
        return $this->belongsTo(SumberTransaksi::class);
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'recurring_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueToRun($query)
    {
        return $query->where('is_active', true)
                    ->where('next_run', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('tanggal_selesai')
                          ->orWhere('tanggal_selesai', '>=', now());
                    });
    }
}
