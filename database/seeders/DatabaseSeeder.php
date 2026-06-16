<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\MasterShift;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class,
            CreateAdminUserSeeder::class,
            MasterKlinikSeeder::class,
            MasterMetodePembayaranSeeder::class,
            MasterShiftSeeder::class,
            MasterDokterSeeder::class,
            UserManagementSeeder::class,
            UserResepsionisSeeder::class,
            UserPerawatSeeder::class,
            RuleInsentifSeeder::class,
        ]);
    }
}
