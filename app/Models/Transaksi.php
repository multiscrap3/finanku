<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use App\Traits\HasAuditLog;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaksi extends Model
{
    use HasFactory, BelongsToHousehold, HasSoftDelete, HasAuditLog;

    protected $table = 'transaksi';

    protected $fillable = [
        'household_id',
        'user_id',
        'kategori_id',
        'sumber_transaksi_id',
        'jenis',
        'jumlah',
        'tanggal',
        'keterangan',
        'bukti_transaksi',
        'ocr_history_id',
        'ocr_items',
        'transfer_ke_id',
        'is_recurring',
        'recurring_id',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date',
        'is_recurring' => 'boolean',
        'ocr_items' => 'array',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Kategori
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Relasi ke Sumber Transaksi
     */
    public function sumberTransaksi(): BelongsTo
    {
        return $this->belongsTo(SumberTransaksi::class);
    }

    /**
     * Relasi ke Sumber Transfer (untuk jenis transfer)
     */
    public function transferKe(): BelongsTo
    {
        return $this->belongsTo(SumberTransaksi::class, 'transfer_ke_id');
    }

    public function ocrHistory(): BelongsTo
    {
        return $this->belongsTo(OcrHistory::class);
    }

    /**
     * Relasi ke Recurring Transaksi
     */
    public function recurringTransaksi(): BelongsTo
    {
        return $this->belongsTo(RecurringTransaksi::class, 'recurring_id');
    }

    /**
     * Relasi ke Tags (many-to-many)
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'transaksi_tags');
    }

    /**
     * Scope berdasarkan jenis
     */
    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * Scope berdasarkan periode
     */
    public function scopeByPeriode($query, $start, $end)
    {
        return $query->whereBetween('tanggal', [$start, $end]);
    }

    /**
     * Scope berdasarkan kategori
     */
    public function scopeByKategori($query, int $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }
}
