<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodicity extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'duration'];

    public function periodicalOfferDetails()
    {
        return $this->hasMany(PeriodicalOfferDetail::class);
    }
}