<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferValidity extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['offer_id', 'valid_from', 'valid_until'];

    protected $dates = ['valid_from', 'valid_until'];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}