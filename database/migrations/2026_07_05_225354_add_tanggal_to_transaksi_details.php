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
        // Hanya tambahkan kolom jika belum ada
        if (!Schema::hasColumn('transaksi_details', 'Tanggal')) {
            Schema::table('transaksi_details', function (Blueprint $table) {
                $table->date('Tanggal')->nullable()->after('IdTransaksi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus kolom hanya jika ada
        if (Schema::hasColumn('transaksi_details', 'Tanggal')) {
            Schema::table('transaksi_details', function (Blueprint $table) {
                $table->dropColumn('Tanggal');
            });
        }
    }
};
