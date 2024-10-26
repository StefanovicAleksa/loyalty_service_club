<?php

namespace Database\Factories;

use App\Models\QrCode;
use App\Models\Customer;
use App\Models\OfferChoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class QrCodeFactory extends Factory
{
    protected $model = QrCode::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'offer_choice_id' => OfferChoice::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'valid_until' => $this->faker->dateTimeBetween('now', '+1 month'),
            'redeemed_at' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
        ];
    }
}