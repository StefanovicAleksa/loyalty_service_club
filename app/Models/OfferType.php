<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferType extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = ['name'];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public static function getTypes(): array
    {
        return ['jednokratna', 'stalna', 'periodična', 'periodična-specijalna'];
    }
}