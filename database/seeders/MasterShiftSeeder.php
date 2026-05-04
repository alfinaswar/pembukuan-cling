<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterShiftSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_shifts')->insert([
            [
                'Nama' => 'Pagi',
                'JamMulai' => '09:00:00',
                'JamSelesai' => '15:00:00',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
            [
                'Nama' => 'Siang',
                'JamMulai' => '15:00:00',
                'JamSelesai' => '21:00:00',
                'UserCreate' => 'system',
                'UserUpdate' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ],
        ]);
    }
}
