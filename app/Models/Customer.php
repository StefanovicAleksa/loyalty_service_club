<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'phone_verified_at'
    ];

    protected $dates = ['phone_verified_at'];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}