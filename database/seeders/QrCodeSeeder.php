<?php

namespace Database\Seeders;

use App\Models\QrCode;
use App\Models\Customer;
use App\Models\OfferChoice;
use Illuminate\Database\Seeder;

class QrCodeSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $offerChoices = OfferChoice::all();

        foreach (range(1, 100) as $index) {
            QrCode::factory()->create([
                'customer_id' => $customers->random()->id,
                'offer_choice_id' => $offerChoices->random()->id,
            ]);
        }
    }
}