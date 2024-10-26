<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'customer_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}