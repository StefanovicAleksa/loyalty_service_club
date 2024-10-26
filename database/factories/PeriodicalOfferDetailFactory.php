<?php

namespace Database\Factories;

use App\Models\PeriodicalOfferDetail;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodicalOfferDetailFactory extends Factory
{
    protected $model = PeriodicalOfferDetail::class;

    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory(),
            'periodicity' => $this->faker->randomElement(['dnenva', 'nedeljna', 'mesečna']),
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'time_of_day_start' => $this->faker->time(),
            'time_of_day_end' => $this->faker->time(),
        ];
    }
}