<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HutangPiutangPembayaran extends Model
{
    use HasFactory;

    protected $table = 'hutang_piutang_pembayaran';

    protected $fillable = [
        'hutang_piutang_id',
        'user_id',
        'sumber_transaksi_id',
        'jumlah',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'jumlah'  => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function hutangPiutang(): BelongsTo
    {
        return $this->belongsTo(HutangPiutang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sumberTransaksi(): BelongsTo
    {
        return $this->belongsTo(SumberTransaksi::class);
    }
}
