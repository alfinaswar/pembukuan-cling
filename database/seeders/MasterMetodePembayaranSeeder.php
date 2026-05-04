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
                'Nama' => 'Cash',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'id' => 2,
                'Nama' => 'Transfer Bank',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'id' => 3,
                'Nama' => 'Debit / Kredit Card',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'id' => 4,
                'Nama' => 'QRIS',
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
        ]);
    }
}
