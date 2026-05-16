<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Spatie\Permission\Models\Role; // Hapus jika tidak pakai Spatie

class UserPerawatSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $rolePerawat = Role::firstOrCreate(['name' => 'Perawat']);
        $perawat = [
            // Cinere
            ['Ade Liya Puti Amanda', 'Perawat', 'Cabang', 'Cinere', 'adeliaaza43@gmail.com'],
            ['Azizah Riskiana', 'Perawat', 'Cabang', 'Cinere', 'azizahriskiana6@gmail.com'],
            ['Dewi Herlyawati', 'Perawat', 'Cabang', 'Cinere', 'dewiherlyawati09@gmail.com'],
            ['Vindy Apriliyandi', 'Perawat', 'Cabang', 'Cinere', 'vindyapriliyandi09@gmail.com'],

            // Serpong
            ['Aulia Fitriani', 'Perawat', 'Cabang', 'Serpong', 'auliafitrianikebidanan@gmail.com'],
            ['Rizki Pani Pical', 'Perawat', 'Cabang', 'Serpong', 'picalrizkifani@gmail.com'],

            // Harapan Indah
            ['Tania Apriyani', 'Perawat', 'Cabang', 'Harapan Indah', 'taniaapriyani55@gmail.com'],
            ['Tiara Miranda', 'Perawat', 'Cabang', 'Harapan Indah', 'tiaramiranda225@gmail.com'],

            // Tebet
            ['Reva Azizah Listiani', 'Perawat', 'Cabang', 'Tebet', 'revaazizah07@gmail.com'],
            ['Nurhasanah', 'Perawat', 'Cabang', 'Tebet', 'nurhasanah99@gmail.com'],
            ['Ayu Chintya Sallamah', 'Perawat', 'Cabang', 'Tebet', 'ayuchintya60@gmail.com'],
            ['Amelia Rostini', 'Perawat', 'Cabang', 'Tebet', 'ameliarostini379@gmail.com'],

            // Ciledug
            ['Salma Reftia Naura', 'Perawat', 'Cabang', 'Ciledug', 'salmareftianaura540@gmail.com'],
            ['Arin Yulia Ningsih', 'Perawat', 'Cabang', 'Ciledug', 'yuliaarin706@gmail.com'],

            // Taman Palem
            ['Rahmi Hermayanti', 'Perawat', 'Cabang', 'Taman Palem', 'rahmihermayanti09@gmail.com'],

            // Jatiasih
            ['Resi Rahayu', 'Perawat', 'Cabang', 'Jatiasih', 'rsirhyu17@gmail.com'],
            ['Safira Hafsari Asror', 'Perawat', 'Cabang', 'Jatiasih', 'safirahf28@gmail.com'],
            ['Indri Febriyanti', 'Perawat', 'Cabang', 'Jatiasih', 'febriyantiindri91@gmail.com'],
            ['Amanda Salsabila zulkarnain', 'Perawat', 'Cabang', 'Jatiasih', 'amandabila14@gmail.com'],
        ];

        $branchCodeMap = [
            'Cinere' => 'KLN-CINERE',
            'Serpong' => 'KLN-SERPONG',
            'Harapan Indah' => 'KLN-HARAPANINDAH',
            'Tebet' => 'KLN-TEBET',
            'Ciledug' => 'KLN-CILEDUG',
            'Taman Palem' => 'KLN-TAMANPALEM',
            'Jatiasih' => 'KLN-JATIASIH',
        ];

        foreach ($perawat as $item) {
            [$name, $roleName, $tipe, $cabang, $email] = $item;
            $username = Str::slug($name, '_');
            $kodeKlinik = $branchCodeMap[$cabang] ?? 'KLN-UNKNOWN';
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => trim($name),
                    'username' => $username,
                    'email_verified_at' => $now,
                    'password' => Hash::make('password'), // Default password
                    'kodeperusahaan' => $kodeKlinik,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            // 3️⃣ Assign role "Perawat" (Spatie)
            if (!$user->hasRole('Perawat')) {
                $user->assignRole($rolePerawat);
            }
        }

        $this->command->info('✅ ' . count($perawat) . ' perawat berhasil dibuat & di-assign role "Perawat".');
    }
}
