<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'event_type',
        'ip_address',
        'user_agent',
        'context',
        'severity',
        'created_at',
    ];

    protected $casts = [
        'context'    => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Catat insiden keamanan — digunakan di seluruh aplikasi.
     */
    public static function record(
        string $eventType,
        string $severity = 'low',
        array $context = [],
        ?int $userId = null,
        ?string $ip = null,
        ?string $userAgent = null,
    ): self {
        return self::create([
            'user_id'    => $userId ?? (auth()->check() ? auth()->id() : null),
            'event_type' => $eventType,
            'severity'   => $severity,
            'context'    => $context ?: null,
            'ip_address' => $ip ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
