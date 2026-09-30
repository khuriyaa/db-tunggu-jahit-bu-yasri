<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'role_name' => 'Admin',
            'description' => 'Pengelola sistem',
        ]);

        Role::create([
            'role_name' => 'Staff',
            'description' => 'Petugas operasional usaha jahit',
        ]);

        Role::create([
            'role_name' => 'Customer',
            'description' => 'Pelanggan usaha jahit',
        ]);
    }
}