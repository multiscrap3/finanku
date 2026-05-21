<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory, BelongsToHousehold;

    protected $fillable = [
        'household_id',
        'nama',
        'slug',
        'warna',
    ];

    public function transaksi(): BelongsToMany
    {
        return $this->belongsToMany(Transaksi::class, 'transaksi_tags');
    }
}
