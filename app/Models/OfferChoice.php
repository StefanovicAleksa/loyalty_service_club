<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class OfferChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id', 
        'name', 
        'description', 
        'image_path', 
        'image_filename', 
        'image_size'
    ];

    protected $dates = ['image_uploaded_at'];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}