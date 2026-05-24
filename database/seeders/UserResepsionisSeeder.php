<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;  // Hapus baris ini jika tidak pakai Spatie

class UserResepsionisSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $roleResepsionis = Role::firstOrCreate(['name' => 'Kasir / Resepsionis']);
        $resepsionis = [
            ['Andhyn Firly Delviana', 'Resepsionis', 'Cabang', 'Cinere', 'andhyn191202@gmail.com'],
            ['Oktavianna Amanda Tempessy', 'Resepsionis', 'Cabang', 'Cinere', 'vianaamanda044@gmail.com'],
            ['Siti Imailah', 'Resepsionis', 'Cabang', 'Serpong', 'pkpsiti42@gmail.com'],
            ['Yuniken Triamanda', 'Resepsionis', 'Cabang', 'Serpong', 'yuniken.triamanda20@gmail.com'],
            ['Risa Marina', 'Resepsionis', 'Cabang', 'Harapan Indah', 'risamrnacc@gmail.com'],
            ['Difa Erikasari', 'Resepsionis', 'Cabang', 'Harapan Indah', 'divaferika7@gmail.com'],
            ['Shela Mitha', 'Resepsionis', 'Cabang', 'Tebet', 'shelamitha26@gmail.com'],
            ['Laela munawaroh', 'Resepsionis', 'Cabang', 'Tebet', 'laellamnwrh@gmail.com'],
            ['Rahma Dwi Hermawati', 'Resepsionis', 'Cabang', 'Ciledug', 'rahmadwihermawatii02@gmail.com'],
            ['Oktavia', 'Resepsionis', 'Cabang', 'Ciledug', 'viaayaa31@gmail.com'],
            ['Tania Olivia Rahmawati', 'Resepsionis', 'Cabang', 'Taman Palem', 'taniaoliviarahmawati@gmail.com'],
            ['Mely Rachmawati', 'Resepsionis', 'Cabang', 'Taman Palem', 'rachmawatimely3@gmail.com'],
            ['Elsa Sinambela', 'Resepsionis', 'Cabang', 'Jatiasih', 'elsacarlsin@gmail.com'],
            ['Rahmadina oktayasima', 'Resepsionis', 'Cabang', 'Jatiasih', 'rahmadinaoktayasjma@gmail.com'],
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

        foreach ($resepsionis as $item) {
            [$name, $roleName, $tipe, $cabang, $email] = $item;

            // Sanitise nama untuk username: "Laela munawaroh" → "laela_munawaroh"
            $username = Str::slug($name, '_');

            // Ambil kode klinik berdasarkan nama cabang
            $kodeKlinik = $branchCodeMap[$cabang] ?? 'KLN-UNKNOWN';

            // 2️⃣ Buat atau update user via Eloquent
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => trim($name),
                    'username' => $username,
                    'email_verified_at' => $now,
                    'password' => Hash::make('password'),
                    'kodeperusahaan' => $kodeKlinik,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            if (!$user->hasRole('Kasir / Resepsionis')) {
                $user->syncRoles($roleResepsionis);
            }
        }

        $this->command->info('✅ ' . count($resepsionis) . ' resepsionis berhasil dibuat & di-assign role "Resepsionis".');
    }
}
