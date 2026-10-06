<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            [
                'service_name' => 'Jahit Baju',
                'service_type' => 'Jahit',
                'base_price' => 150000,
                'estimated_days' => 5,
            ],
            [
                'service_name' => 'Permak Celana',
                'service_type' => 'Permak',
                'base_price' => 30000,
                'estimated_days' => 2,
            ],
            [
                'service_name' => 'Permak Baju',
                'service_type' => 'Permak',
                'base_price' => 40000,
                'estimated_days' => 2,
            ],
        ] as $service) {
            Service::firstOrCreate(
                ['service_name' => $service['service_name']],
                $service,
            );
        }
    }
}