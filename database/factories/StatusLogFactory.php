<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::inRandomOrder()->first()?->id ?? Order::factory(),
            'updated_by_user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'status' => fake()->randomElement([
                'Menunggu',
                'Diproses',
                'Selesai',
                'Diambil',
            ]),
            'description' => fake()->sentence(),
            'changed_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}