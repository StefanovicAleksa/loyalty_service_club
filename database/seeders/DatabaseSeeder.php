<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CustomerSeeder::class,
            UserSeeder::class,
            OfferTypeSeeder::class,
            OfferSeeder::class,
            QrCodeSeeder::class,
            MessageSeeder::class,
        ]);
    }
}
