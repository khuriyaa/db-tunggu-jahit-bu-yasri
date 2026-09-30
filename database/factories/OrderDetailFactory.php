<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'service_id' => Service::query()->inRandomOrder()->first()?->id ?? 1,
            'clothing_type' => fake()->randomElement([
                'Kemeja',
                'Celana',
                'Dress',
                'Rok',
                'Jaket',
            ]),
            'quantity' => fake()->numberBetween(1, 5),
            'price' => fake()->randomFloat(2, 10000, 500000),
            'note' => fake()->optional()->sentence(),
        ];
    }
}