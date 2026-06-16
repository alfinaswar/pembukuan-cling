<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Role permissions
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            // Product permissions (your old seed)
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            // Custom permissions from your table screenshot
            'Admin Cabang',
            'pembayaran-index',
            'masterdata',
            'laporan',
            'laporan-umum',
            'laporan-perawat',
            'laporan-dokter',
            'laporan-resepsionis',
            'laporan-kasir',
            'master-perawatan',
            'master-dokter',
            'master-perawat',
            'master-kasir',
            'master-shift',
            'master-metodepembayaran',
            'master-user',
            'insentif',
            'pengaturan',
            'master-klinik',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission], ['guard_name' => 'web']);
        }
    }
}
