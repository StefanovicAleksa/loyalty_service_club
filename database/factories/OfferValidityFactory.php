<?php

namespace Database\Factories;

use App\Models\OfferValidity;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferValidityFactory extends Factory
{
    protected $model = OfferValidity::class;

    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory(),
            'valid_from' => $this->faker->dateTimeBetween('now', '+1 month'),
            'valid_until' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
        ];
    }
}