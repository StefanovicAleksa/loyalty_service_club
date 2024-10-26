<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        Customer::all()->each(function ($customer) {
            User::factory()->create(['customer_id' => $customer->id]);
        });
    }
}