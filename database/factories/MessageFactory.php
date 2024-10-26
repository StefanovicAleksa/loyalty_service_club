<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'content' => $this->faker->paragraph,
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}