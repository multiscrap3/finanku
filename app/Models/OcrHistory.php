<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OcrHistory extends Model
{
    use HasFactory, BelongsToHousehold;

    protected $table = 'ocr_history';

    protected $fillable = [
        'household_id',
        'user_id',
        'transaksi_id',
        'image_path',
        'extracted_data',
        'confidence_score',
        'status',
    ];

    protected $casts = [
        'extracted_data' => 'array',
        'confidence_score' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }
}
