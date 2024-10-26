<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'offer_type_id'];

    public function offerType()
    {
        return $this->belongsTo(OfferType::class);
    }

    public function validity()
    {
        return $this->hasOne(OfferValidity::class);
    }

    public function periodicalDetails()
    {
        return $this->hasOne(PeriodicalOfferDetail::class);
    }

    public function periodicity()
    {
        return $this->hasOneThrough(Periodicity::class, PeriodicalOfferDetail::class);
    }

    public function choices()
    {
        return $this->hasMany(OfferChoice::class);
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }
}