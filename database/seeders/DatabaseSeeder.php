<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\StatusLog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ServiceSeeder::class,
        ]);

        User::factory(10)->create();

        Customer::factory(10)->create();

        Order::factory(15)->create();

        OrderDetail::factory(30)->create();

        StatusLog::factory(20)->create();
    }
}