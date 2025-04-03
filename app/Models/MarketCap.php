<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketCap extends Model
{
    use HasFactory;

    protected $fillable = [
        'time_stamp',
        'market_cap_in_tr_dollars',
        'market_cap_in_lac_cr_rupees',
        'market_cap_in_cr_rupees',
        'market_cap_in_cr_rupees_formatted',
        'market_cap_in_lac_cr_rupees_formatted',
        'underlying',
    ];

    protected $casts = [
        'time_stamp' => 'date',
        'market_cap_in_tr_dollars' => 'float',
        'market_cap_in_lac_cr_rupees' => 'float',
        'market_cap_in_cr_rupees' => 'float',
    ];
}