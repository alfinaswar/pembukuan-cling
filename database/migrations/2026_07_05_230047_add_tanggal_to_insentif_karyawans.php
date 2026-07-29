<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('insentif_karyawans', 'Tanggal')) {
            Schema::table('insentif_karyawans', function (Blueprint $table) {
                $table->date('Tanggal')->nullable()->after('IdTransaksi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('insentif_karyawans', 'Tanggal')) {
            Schema::table('insentif_karyawans', function (Blueprint $table) {
                $table->dropColumn('Tanggal');
            });
        }
    }
};
