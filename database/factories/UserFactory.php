<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ];
    }
}