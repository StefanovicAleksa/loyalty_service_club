<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'offer_choice_id',
        'created_at',
        'valid_until',
        'redeemed_at'
    ];

    protected $dates = ['created_at', 'valid_until', 'redeemed_at'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function offerChoice()
    {
        return $this->belongsTo(OfferChoice::class);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();

        static::created(function ($qrCode) {
            // This will trigger the observer's created method
        });
    }
}