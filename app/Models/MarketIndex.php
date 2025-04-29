<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketIndex extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'symbol',
        'status'
    ];

    /**
     * Get the pre-open market data associated with this index.
     */
    public function preOpenMarketData()
    {
        return $this->hasMany(PreOpenMarketData::class, 'symbol', 'symbol');
    }

    /**
     * Get the market index data for this index.
     */
    public function marketIndexData()
    {
        return $this->hasMany(MarketIndexData::class, 'symbol_id', 'id');
    }
}