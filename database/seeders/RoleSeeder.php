<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use LogicException;
use RuntimeException;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::updateOrCreate(
            ['role_name' => 'Admin'],
            ['description' => 'Pengelola sistem'],
        );

        $ownerRole = Role::updateOrCreate(
            ['role_name' => 'Owner'],
            ['description' => 'Pemilik usaha dengan akses laporan pesanan dan keuangan'],
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

        $ownerEmail = config('owner.email');
        $ownerPassword = config('owner.password');

        if (blank($ownerEmail) !== blank($ownerPassword)) {
            throw new RuntimeException('Set both OWNER_EMAIL and OWNER_PASSWORD in your .env file to create an owner account.');
        }

        if (filled($ownerEmail) && filled($ownerPassword) && strcasecmp($ownerEmail, $adminEmail) === 0) {
            throw new LogicException('OWNER_EMAIL must be different from ADMIN_EMAIL.');
        }

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'role_id' => $adminRole->id,
                'name' => config('admin.name'),
                'password' => $adminPassword,
            ],
        );

        if (filled($ownerEmail) && filled($ownerPassword)) {
            User::updateOrCreate(
                ['email' => $ownerEmail],
                [
                    'role_id' => $ownerRole->id,
                    'name' => config('owner.name'),
                    'password' => $ownerPassword,
                ],
            );
        }
    }
}
