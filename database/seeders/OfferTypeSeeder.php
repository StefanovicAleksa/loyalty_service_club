<?php

namespace Database\Seeders;

use App\Models\OfferType;
use Illuminate\Database\Seeder;

class OfferTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (OfferType::getTypes() as $type) {
            OfferType::firstOrCreate(['name' => $type]);
        }
    }
}