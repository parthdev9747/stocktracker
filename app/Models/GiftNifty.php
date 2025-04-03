<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftNifty extends Model
{
    use HasFactory;

    protected $table = 'gift_niftys';

    protected $fillable = [
        'instrument_type',
        'symbol',
        'expiry_date',
        'option_type',
        'strike_price',
        'last_price',
        'day_change',
        'per_change',
        'contracts_traded',
        'timestamp',
        'external_id',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'last_price' => 'float',
        'contracts_traded' => 'integer',
    ];
}
