<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
   public function definition(): array
{
    return [
        'order_code' => fake()->unique()->bothify('ORD-####'),
        'queue_number' => fake()->unique()->numerify('Q-###'),
        'customer_id' => Customer::query()->inRandomOrder()->first()?->id ?? Customer::factory(),
        'user_id' => User::query()->inRandomOrder()->first()?->id ?? User::factory(),
        'order_date' => fake()->date(),
        'estimated_completion_date' => fake()->dateTimeBetween('now', '+14 days'),
        'current_status' => fake()->randomElement([
            'Menunggu',
            'Diproses',
            'Selesai',
            'Diambil',
        ]),
        'total_items' => fake()->numberBetween(1, 10),
        'total_price' => fake()->randomFloat(2, 10000, 1000000),
    ];
}
}