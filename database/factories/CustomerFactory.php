<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        static $phoneNumber = 1000000000;

        return [
            'name' => $this->faker->name,
            'phone' => $this->generateUniquePhoneNumber($phoneNumber++),
            'phone_verified_at' => $this->faker->optional()->dateTime(),
        ];
    }

    private function generateUniquePhoneNumber(int $number): string
    {
        // Ensure the number is always 10 digits long
        $formattedNumber = str_pad($number, 10, '0', STR_PAD_LEFT);
        return '+' . $formattedNumber;
    }
}