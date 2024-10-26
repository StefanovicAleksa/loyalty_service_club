<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodicalOfferDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'offer_id',
        'periodicity_id',
        'day_of_week',
        'time_of_day_start',
        'time_of_day_end'
    ];

    protected $casts = [
        'time_of_day_start' => 'datetime',
        'time_of_day_end' => 'datetime',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function periodicity()
    {
        return $this->belongsTo(Periodicity::class);
    }
}