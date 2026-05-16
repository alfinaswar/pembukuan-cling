<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterKlinikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userId = 1;

        $clinics = [
            [
                'Kode' => 'KLN-CINERE',
                'Nama' => 'Cinere',
                'Alamat' => 'Jl. Cinere Raya No. 1, Depok',
                'NoTelp' => '021-7540001',
                'Email' => 'cinere@klinik.example.com',
                'Status' => 'active',
                'UserCreate' => $userId,
                'UserUpdate' => $userId,
                'UserDelete' => null,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'Kode' => 'KLN-SERPONG',
                'Nama' => 'Serpong',
                'Alamat' => 'Jl. BSD Raya Utama, Tangerang Selatan',
                'NoTelp' => '021-5380002',
                'Email' => 'serpong@klinik.example.com',
                'Status' => 'active',
                'UserCreate' => $userId,
                'UserUpdate' => $userId,
                'UserDelete' => null,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'Kode' => 'KLN-HARAPANINDAH',
                'Nama' => 'Harapan Indah',
                'Alamat' => 'Jl. Harapan Indah Boulevard, Bekasi',
                'NoTelp' => '021-8240003',
                'Email' => 'harapanindah@klinik.example.com',
                'Status' => 'active',
                'UserCreate' => $userId,
                'UserUpdate' => $userId,
                'UserDelete' => null,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'Kode' => 'KLN-TEBET',
                'Nama' => 'Tebet',
                'Alamat' => 'Jl. Tebet Barat Dalam, Jakarta Selatan',
                'NoTelp' => '021-8370004',
                'Email' => 'tebet@klinik.example.com',
                'Status' => 'active',
                'UserCreate' => $userId,
                'UserUpdate' => $userId,
                'UserDelete' => null,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'Kode' => 'KLN-CILEDUG',
                'Nama' => 'Ciledug',
                'Alamat' => 'Jl. Ciledug Raya, Tangerang',
                'NoTelp' => '021-5840005',
                'Email' => 'ciledug@klinik.example.com',
                'Status' => 'active',
                'UserCreate' => $userId,
                'UserUpdate' => $userId,
                'UserDelete' => null,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'Kode' => 'KLN-TAMANPALEM',
                'Nama' => 'Taman Palem',
                'Alamat' => 'Jl. Taman Palem Lestari, Jakarta Barat',
                'NoTelp' => '021-5890006',
                'Email' => 'tamanpalem@klinik.example.com',
                'Status' => 'active',
                'UserCreate' => $userId,
                'UserUpdate' => $userId,
                'UserDelete' => null,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'Kode' => 'KLN-JATIASIH',
                'Nama' => 'Jatiasih',
                'Alamat' => 'Jl. Jatiasih Raya, Bekasi',
                'NoTelp' => '021-8490007',
                'Email' => 'jatiasih@klinik.example.com',
                'Status' => 'active',
                'UserCreate' => $userId,
                'UserUpdate' => $userId,
                'UserDelete' => null,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('master_kliniks')->insert($clinics);
    }
}
