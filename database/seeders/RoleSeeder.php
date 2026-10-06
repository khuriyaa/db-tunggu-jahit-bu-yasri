<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::updateOrCreate(
            ['role_name' => 'Admin'],
            ['description' => 'Pengelola sistem'],
        );

        Role::updateOrCreate(
            ['role_name' => 'Staff'],
            ['description' => 'Petugas operasional usaha jahit'],
        );

        Role::updateOrCreate(
            ['role_name' => 'Customer'],
            ['description' => 'Pelanggan usaha jahit'],
        );

        $adminEmail = config('admin.email');
        $adminPassword = config('admin.password');

        if (blank($adminEmail) || blank($adminPassword)) {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD in your .env file before seeding roles.');
        }

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'role_id' => $adminRole->id,
                'name' => config('admin.name'),
                'password' => $adminPassword,
            ],
        );
    }
}
