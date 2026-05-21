<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportBank extends Model
{
    use HasFactory, BelongsToHousehold;

    protected $table = 'import_bank';

    protected $fillable = [
        'household_id',
        'user_id',
        'sumber_transaksi_id',
        'file_path',
        'file_name',
        'total_rows',
        'imported_rows',
        'failed_rows',
        'errors',
        'status',
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'imported_rows' => 'integer',
        'failed_rows' => 'integer',
        'errors' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function sumberTransaksi(): BelongsTo
    {
        return $this->belongsTo(SumberTransaksi::class);
    }

    public function markProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    public function markCompleted(int $totalRows, int $importedRows, int $failedRows, array $errors = []): void
    {
        $this->update([
            'total_rows' => $totalRows,
            'imported_rows' => $importedRows,
            'failed_rows' => $failedRows,
            'errors' => $errors ?: null,
            'status' => $failedRows > 0 ? 'failed' : 'completed',
        ]);
    }

    public function markFailed(array $errors): void
    {
        $this->update([
            'errors' => $errors,
            'status' => 'failed',
        ]);
    }
}