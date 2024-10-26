<?php

namespace Database\Factories;

use App\Models\OfferChoice;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferChoiceFactory extends Factory
{
    protected $model = OfferChoice::class;

    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory(),
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'picture' => base64_encode($this->faker->image(null, 640, 480, null, false)),
        ];
    }
}