<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat role Superadmin jika belum ada
        $role = Role::firstOrCreate(['name' => 'Superadmin']);

        // Buat hanya satu akun untuk Superadmin
        $user = User::firstOrCreate(
            [
                'email' => 'superadmin@admin.com',
            ],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('123456'),
                'kodeperusahaan' => 'SUP-ADMIN',
            ]
        );

        // Assign role Superadmin ke user
        $user->assignRole($role);
    }
}
