<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;  // Hapus jika tidak pakai Spatie

class UserManagementSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1️⃣ Pastikan role "Super Admin" tersedia (Spatie)
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);

        // Format: [Nama, Kategori, Role, Tipe, Email]
        $management = [
            ['Riyan Baharudin', 'Management', 'Super Admin', 'Pusat', 'riyan.baharudin@gmail.com'],
            ['Niky Ustrina', 'Management', 'Super Admin', 'Pusat', 'niky.ustrinaa@gmail.com'],
            ['Wanda Silviana', 'Management', 'Super Admin', 'Pusat', 'wandasilviana@gmail.com'],
            ['Yohana Pandiangan', 'Management', 'Super Admin', 'Pusat', 'yohanapandiangan6@gmail.com'],
            ['Dinda Gustika', 'Management', 'Super Admin', 'Pusat', 'gustikadinda@gmail.com'],
            ['Seby Shafa Shanazbila Soraya', 'Management', 'Super Admin', 'Pusat', 'nanasbilah17@gmail.com'],
        ];

        // Mapping tipe lokasi → kode perusahaan
        // "Pusat" menggunakan kode khusus untuk head office
        $branchCodeMap = [
            'Pusat' => 'KLN-PUSAT',
            'Cinere' => 'KLN-CINERE',
            'Serpong' => 'KLN-SERPONG',
            'Harapan Indah' => 'KLN-HARAPANINDAH',
            'Tebet' => 'KLN-TEBET',
            'Ciledug' => 'KLN-CILEDUG',
            'Taman Palem' => 'KLN-TAMANPALEM',
            'Jatiasih' => 'KLN-JATIASIH',
        ];

        foreach ($management as $item) {
            [$name, $kategori, $roleName, $tipe, $email] = $item;

            // Generate username: "Riyan Baharudin" → "riyan_baharudin"
            $username = Str::slug($name, '_');

            // Ambil kode perusahaan berdasarkan tipe/cabang
            $kodePerusahaan = $branchCodeMap[$tipe] ?? 'KLN-UNKNOWN';

            // 2️⃣ Buat atau update user via Eloquent
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => trim($name),
                    'username' => $username,
                    'email_verified_at' => $now,
                    'password' => Hash::make('password'),  // Default password
                    'kodeperusahaan' => $kodePerusahaan,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            // 3️⃣ Assign role "Super Admin" (Spatie)
            if (!$user->hasRole('Super Admin')) {
                $user->assignRole($roleSuperAdmin);
            }
        }

        $this->command->info('✅ ' . count($management) . ' user management berhasil dibuat & di-assign role "Super Admin".');
    }
}
