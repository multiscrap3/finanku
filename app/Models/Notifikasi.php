<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    use HasFactory, BelongsToHousehold;

    protected $table = 'notifikasi';

    protected $fillable = [
        'household_id',
        'user_id',
        'judul',
        'pesan',
        'jenis',
        'link',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
