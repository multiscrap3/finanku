<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'payment_history';

    protected $fillable = [
        'household_id',
        'plan_id',
        'amount',
        'payment_method',
        'payment_reference',
        'status',
        'payment_date',
        'period_start',
        'period_end',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
