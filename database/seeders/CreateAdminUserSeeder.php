<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hanya Untuk 7 Cabang CLING
        $companies = [
            [
                'Kode' => 'CLING1',
                'Nama' => 'CLING1',
            ],
            [
                'Kode' => 'CLING2',
                'Nama' => 'CLING2',
            ],
            [
                'Kode' => 'CLING3',
                'Nama' => 'CLING3',
            ],
            [
                'Kode' => 'CLING4',
                'Nama' => 'CLING4',
            ],
            [
                'Kode' => 'CLING5',
                'Nama' => 'CLING5',
            ],
            [
                'Kode' => 'CLING6',
                'Nama' => 'CLING6',
            ],
            [
                'Kode' => 'CLING7',
                'Nama' => 'CLING7',
            ],
        ];

        $role = Role::firstOrCreate(['name' => 'Admin Cabang']);
        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions);

        foreach ($companies as $company) {
            $email = strtolower($company['Kode']) . '@admin.com';
            $user = User::firstOrCreate(
                [
                    'email' => $email,
                ],
                [
                    'name' => 'Administrator ' . $company['Nama'],
                    'password' => bcrypt('123456'),
                    'kodeperusahaan' => $company['Kode'],
                ]
            );
            $user->assignRole([$role->id]);
        }
    }
}
