<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketIndexData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'symbol_id',
        'open',
        'high',
        'low',
        'close',
        'volume',
        'time_frame'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'open' => 'float',
        'high' => 'float',
        'low' => 'float',
        'close' => 'float',
        'volume' => 'integer'
    ];

    /**
     * Get the market index that owns this data.
     */
    public function marketIndex()
    {
        return $this->belongsTo(MarketIndex::class, 'symbol_id', 'id');
    }
}