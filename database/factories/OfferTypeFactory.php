<?php

namespace Database\Factories;

use App\Models\OfferType;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferTypeFactory extends Factory
{
    protected $model = OfferType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(OfferType::getTypes()),
        ];
    }
}