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
        $roleDokter = Role::firstOrCreate(['name' => 'Dokter']);
        $doctorsMulti = [
            ['Monica Dwi Anggraini', ['KLN-CINERE']],
            ['Octavia Andina Putri', ['KLN-CINERE']],
            ['Nicholas Limanda', ['KLN-CINERE', 'KLN-SERPONG']],
            ['Stella Advena Anindita', ['KLN-CINERE']],
            ['Rizka Putri Apriandini', ['KLN-CINERE']],
            ['Mutiara Ayu Sisworini', ['KLN-CINERE']],
            ['Tifani Sandri', ['KLN-CINERE']],
            ['Sofie Bosoma Syamra', ['KLN-CINERE']],
            ['Nadine Khalissya', ['KLN-CINERE']],
            ['Nathassa Astrioni', ['KLN-CINERE', 'KLN-CILEDUG']],
            ['Tiara Monica Safiera', ['KLN-CINERE']],
            ['Vidya Asri Ayuningtyas', ['KLN-HARAPANINDAH', 'KLN-CINERE', 'KLN-TEBET']],
            // SERPONG
            ['Grace Esther', ['KLN-SERPONG']],  // Per prompt: Christien Grace Esther Maria, di Serpong (satu nama, asumsikan Grace Esther sbg formal)
            ['Yessy Josephine Sijabat', ['KLN-SERPONG']],
            ['Meilina Fatimah', ['KLN-SERPONG']],
            ['Niky Ustrina', ['KLN-SERPONG']],
            ['Chintia Herrera', ['KLN-SERPONG']],
            // HARAPAN INDAH & JATIASIH & TEBET
            ['Esther Julita Palupi', ['KLN-HARAPANINDAH', 'KLN-JATIASIH']],
            ['Annisa Mazaya', ['KLN-HARAPANINDAH', 'KLN-JATIASIH']],
            ['Shinta Dewi Ritati', ['KLN-HARAPANINDAH', 'KLN-JATIASIH']],
            ['Aldy Anzhari Ayub', ['KLN-HARAPANINDAH', 'KLN-JATIASIH']],
            ['Calvin Pascananda', ['KLN-HARAPANINDAH', 'KLN-TEBET']],
            // TEBET
            ['Siti Hanna Yavitha', ['KLN-TEBET']],  // Typo pada prompt bisa "Yasvitha" → "Yavitha"
            ['Nada Avaffia', ['KLN-TEBET']],
            ['Devasya Nathania Kamilla', ['KLN-TEBET']],
            ['Rais Dzakwan Hidayatullah', ['KLN-TEBET']],
            ['Amellia Sekar Ramadhani', ['KLN-TEBET']],
            ['Catherine', ['KLN-TEBET']],
            ['Ni Komang P. Pradianty', ['KLN-TEBET']],
            ['Jessica Quiteria Florenthe', ['KLN-TEBET']],
            ['Hanna Safira', ['KLN-TEBET']],
            ['Hammam Habib Al Falah', ['KLN-TEBET']],
            ['Sabrina Annisa', ['KLN-TEBET']],
            // CILEDUG
            ['Suci Mumpuni Pekerti', ['KLN-CILEDUG']],
            ['Nathania Chrisnovita M.', ['KLN-CILEDUG']],
            ['Faradina Azzahra', ['KLN-CILEDUG']],
            ['Putu Natasha Diska Agusjaya', ['KLN-CILEDUG']],
            // TAMAN PALEM
            ['Imammuddin Thaariq', ['KLN-TAMANPALEM']],
            ['Raisa Milenia Syukma', ['KLN-TAMANPALEM']],
            ['Salsa Nabila Evandi', ['KLN-TAMANPALEM']],
            // JATIASIH
            ['Morita Ananda Baihaty', ['KLN-JATIASIH']],
            ['Urfi Fadhilah', ['KLN-JATIASIH']],
            ['Tri Utomo', ['KLN-JATIASIH']],
            ['Christianus George A.', ['KLN-JATIASIH']],
            ['Muthia Alifah Khansa', ['KLN-JATIASIH']],
            ['Methildis Victoria Donya Asri', ['KLN-JATIASIH']],
        ];

        $seen = [];

        foreach ($doctorsMulti as [$name, $kodeKliniks]) {
            foreach ($kodeKliniks as $kodeKlinik) {
                $uniqueKey = $name . '|' . $kodeKlinik;
                if (isset($seen[$uniqueKey]))
                    continue;
                $seen[$uniqueKey] = true;

                // Email harus unik, jadi jika dokter ada di lebih dari satu klinik, pake kode klinik sebagai suffix
                $emailName = Str::slug($name, '.');
                $email = $emailName . '.' . strtolower($kodeKlinik) . '@klinik.example.com';

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

                // Assign role "Dokter"
                if (!$user->hasRole('Dokter')) {
                    $user->syncRoles($roleDokter);
                }
            }
        }

        $this->command->info('✅ ' . count($seen) . ' dokter berhasil dibuat & di-assign role "Dokter".');
    }
}
