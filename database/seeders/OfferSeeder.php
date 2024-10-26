<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\OfferValidity;
use App\Models\PeriodicalOfferDetail;
use App\Models\OfferChoice;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        Offer::factory()
            ->count(20)
            ->create()
            ->each(function ($offer) {
                // Create offer validity
                OfferValidity::factory()->create([
                    'offer_id' => $offer->id,
                ]);

                // Create periodical offer details for some offers
                if ($offer->offerType->name === 'periodična' || $offer->offerType->name === 'periodična-specijalna') {
                    PeriodicalOfferDetail::factory()->create([
                        'offer_id' => $offer->id,
                    ]);
                }

                // Create offer choices
                OfferChoice::factory()->count(rand(1, 3))->create([
                    'offer_id' => $offer->id,
                ]);
            });
    }
}