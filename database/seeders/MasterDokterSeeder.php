<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class MasterDokterSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1️⃣ Pastikan role "Dokter" tersedia
        $roleDokter = Role::firstOrCreate(['name' => 'Dokter']);

        $doctors = [
            // CINERE
            ['Tiara Monica Safiera', 'KLN-CINERE'],
            ['Octavia Andina Putri', 'KLN-CINERE'],
            ['Nadine Khalissya', 'KLN-CINERE'],
            ['Tifani Sandri', 'KLN-CINERE'],
            ['Nicholas Limanda', 'KLN-CINERE'],
            ['Monica Dwi Anggraini', 'KLN-CINERE'],
            ['Rizka Putri Apriandini', 'KLN-CINERE'],
            ['Vidya Asri Ayuningtyas', 'KLN-CINERE'],
            ['Stella Advena Anindita', 'KLN-CINERE'],
            ['Sofie Bosoma Syamra', 'KLN-CINERE'],
            ['Mutiara Ayu Sisworini', 'KLN-CINERE'],
            ['Nathassa Astrioni', 'KLN-CINERE'],
            // SERPONG
            ['Grace Esther', 'KLN-SERPONG'],
            ['Yessy Josephine Sijabat', 'KLN-SERPONG'],
            ['Meilina Fatimah', 'KLN-SERPONG'],
            ['Niky Ustrina', 'KLN-SERPONG'],
            ['Chintia Herrera', 'KLN-SERPONG'],
            // HARAPAN INDAH
            ['Shinta Dewi Ritati', 'KLN-HARAPANINDAH'],
            ['Calvin Pascananda', 'KLN-HARAPANINDAH'],
            ['Annisa Mazaya', 'KLN-HARAPANINDAH'],
            ['Esther Julita Palupi', 'KLN-HARAPANINDAH'],
            ['Aldy Anzhari Ayub', 'KLN-HARAPANINDAH'],
            // TEBET
            ['Siti Hanna Yasvitha', 'KLN-TEBET'],
            ['Nada Avaffia', 'KLN-TEBET'],
            ['Hammam Habib Al Falah', 'KLN-TEBET'],
            ['Rais Dzakwan Hidayatullah', 'KLN-TEBET'],
            ['Devasya Nathania Kamilla', 'KLN-TEBET'],
            ['Amellia Sekar Ramadhani', 'KLN-TEBET'],
            ['Catherine', 'KLN-TEBET'],
            ['Ni Komang P. Pradianty', 'KLN-TEBET'],
            ['Hanna Safira', 'KLN-TEBET'],
            // CILEDUG
            ['Putu Natasha Diska Agusjaya', 'KLN-CILEDUG'],
            ['Nathania Chrisnovita M.', 'KLN-CILEDUG'],
            ['Suci Mumpuni Pekerti', 'KLN-CILEDUG'],
            ['Faradina Azzahra', 'KLN-CILEDUG'],
            // TAMAN PALEM
            ['Salsa Nabila Evandi', 'KLN-TAMANPALEM'],
            ['Imammuddin Thaariq', 'KLN-TAMANPALEM'],
            ['Putri Askia', 'KLN-TAMANPALEM'],
            ['Raisa Milenia Syukma', 'KLN-TAMANPALEM'],
            // JATIASIH
            ['Tri Utomo', 'KLN-JATIASIH'],
            ['Muthia Alifah Khansa', 'KLN-JATIASIH'],
            ['Morita Ananda Baihaty', 'KLN-JATIASIH'],
            ['Christianus George A.', 'KLN-JATIASIH'],
            ['Urfi Fadhilah', 'KLN-JATIASIH'],
            ['Methildis', 'KLN-JATIASIH'],
        ];

        $seenNames = [];

        foreach ($doctors as $doctor) {
            $name = $doctor[0];
            $kodeKlinik = $doctor[1];

            // Skip duplikat nama
            if (in_array($name, $seenNames))
                continue;
            $seenNames[] = $name;

            $email = Str::slug($name, '.') . '@klinik.example.com';

            // 2️⃣ Buat atau ambil user via Eloquent
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'email_verified_at' => $now,
                    'password' => Hash::make('password'),
                    'kodeperusahaan' => $kodeKlinik,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            // 3️⃣ Assign role "Dokter"
            if (!$user->hasRole('Dokter')) {
                $user->syncRoles($roleDokter);
            }
        }

        $this->command->info('✅ ' . count($seenNames) . ' dokter berhasil dibuat & di-assign role "Dokter".');
    }
}
