<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuleInsentifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rule_insentifs')->insert([

            // =========================================================
            // INSENTIF RESEPSIONIS
            // =========================================================

            [
                'Role' => 3,
                'JenisRule' => 'omzet_shift',
                'Operator' => '>=',
                'Nilai' => '6000000',
                'Nominal' => 100000,
                'TipeNominal' => 'fixed',
                'BerlakuPer' => 'shift',
                'KondisiTambahan' => null,
                'Keterangan' => 'Omzet klinik per shift minimum 6 jt',
                'Status' => 1,
                'KodeCabang' => null,
                'UserCreate' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'Role' => 3,
                'JenisRule' => 'omzet_shift',
                'Operator' => '>=',
                'Nilai' => '12000000',
                'Nominal' => 200000,
                'TipeNominal' => 'fixed',
                'BerlakuPer' => 'shift',
                'KondisiTambahan' => null,
                'Keterangan' => 'Omzet klinik per shift minimum 12 jt',
                'Status' => 1,
                'KodeCabang' => null,
                'UserCreate' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'Role' => 3,
                'JenisRule' => 'pasien_lama',
                'Operator' => '>=',
                'Nilai' => '8',
                'Nominal' => 30000,
                'TipeNominal' => 'fixed',
                'BerlakuPer' => 'shift',
                'KondisiTambahan' => null,
                'Keterangan' => 'Minimal 8 pasien lama per shift',
                'Status' => 1,
                'KodeCabang' => null,
                'UserCreate' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // =========================================================
            // INSENTIF PERAWAT
            // =========================================================

            [
                'Role' => 4,
                'JenisRule' => 'omzet_shift',
                'Operator' => '>=',
                'Nilai' => '6000000',
                'Nominal' => 50000,
                'TipeNominal' => 'fixed',
                'BerlakuPer' => 'shift',
                'KondisiTambahan' => null,
                'Keterangan' => 'Omzet klinik per shift minimum 6 jt',
                'Status' => 1,
                'KodeCabang' => null,
                'UserCreate' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'Role' => 4,
                'JenisRule' => 'omzet_shift',
                'Operator' => '>=',
                'Nilai' => '12000000',
                'Nominal' => 100000,
                'TipeNominal' => 'fixed',
                'BerlakuPer' => 'shift',
                'KondisiTambahan' => null,
                'Keterangan' => 'Omzet klinik per shift minimum 12 jt',
                'Status' => 1,
                'KodeCabang' => null,
                'UserCreate' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'Role' => 4,
                'JenisRule' => 'pasien_lama',
                'Operator' => '>=',
                'Nilai' => '8',
                'Nominal' => 30000,
                'TipeNominal' => 'fixed',
                'BerlakuPer' => 'shift',
                'KondisiTambahan' => null,
                'Keterangan' => 'Minimal 8 pasien lama per shift',
                'Status' => 1,
                'KodeCabang' => null,
                'UserCreate' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'Role' => 4,
                'JenisRule' => 'transaksi',
                'Operator' => '>=',
                'Nilai' => '1000000',
                'Nominal' => 10000,
                'TipeNominal' => 'fixed',
                'BerlakuPer' => 'transaksi',
                'KondisiTambahan' => null,
                'Keterangan' => 'Billing pasien minimum 1 jt per transaksi',
                'Status' => 1,
                'KodeCabang' => null,
                'UserCreate' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'Role' => 4,
                'JenisRule' => 'tindakan',
                'Operator' => '=',
                'Nilai' => 'Odontektomi',
                'Nominal' => 25000,
                'TipeNominal' => 'fixed',
                'BerlakuPer' => 'transaksi',
                'KondisiTambahan' => null,
                'Keterangan' => 'Jenis perawatan Odontektomi',
                'Status' => 1,
                'KodeCabang' => null,
                'UserCreate' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'Role' => 4,
                'JenisRule' => 'pasien_baru',
                'Operator' => '>=',
                'Nilai' => '1',
                'Nominal' => 2000,
                'TipeNominal' => 'fixed',
                'BerlakuPer' => 'transaksi',
                'KondisiTambahan' => null,
                'Keterangan' => 'Pasien baru',
                'Status' => 1,
                'KodeCabang' => null,
                'UserCreate' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
