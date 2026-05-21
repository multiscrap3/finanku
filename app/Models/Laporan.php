<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporan extends Model
{
    use HasFactory, BelongsToHousehold;

    protected $table = 'laporan';

    protected $fillable = [
        'household_id',
        'user_id',
        'jenis',
        'periode_mulai',
        'periode_selesai',
        'file_path',
        'format',
        'status',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('jenis', $jenis);
    }
}
