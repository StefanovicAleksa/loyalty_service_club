<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\OfferType;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'offer_type_id' => function () {
                return OfferType::inRandomOrder()->first()->id;
            },
        ];
    }
}