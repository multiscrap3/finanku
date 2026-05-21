<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TabunganTransaksi extends Model
{
    use HasFactory;

    protected $table = 'tabungan_transaksi';

    protected $fillable = [
        'tabungan_id',
        'user_id',
        'jenis',
        'jumlah',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function tabungan(): BelongsTo
    {
        return $this->belongsTo(Tabungan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
