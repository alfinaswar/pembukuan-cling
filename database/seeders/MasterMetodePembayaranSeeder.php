<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterMetodePembayaranSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_metode_pembayarans')->insert([
            [
                'id' => 1,
                'Nama' => 'EDC BCA',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'id' => 2,
                'Nama' => 'EDC BNI',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'id' => 3,
                'Nama' => 'EDC MANDIRI',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'id' => 4,
                'Nama' => 'CASH',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'id' => 5,
                'Nama' => 'ASURANSI ADMEDIKA',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'id' => 6,
                'Nama' => 'ASURANSI FULLERTON',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'id' => 7,
                'Nama' => 'ASURANSI GARDAMEDIKA',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'id' => 8,
                'Nama' => 'ASURANSI OWLEXA',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'id' => 9,
                'Nama' => 'CARENOW',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'id' => 10,
                'Nama' => 'KREDIVO',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
        ]);
    }
}
