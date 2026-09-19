<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stok', function (Blueprint $table) {
            // Tambah kolom DentalUnit
            $table->string('DentalUnit')->nullable()->after('KodeKlinik');
            $table->dropUnique(['KodeKlinik', 'BarangId']); // Hapus unique lama
            $table->unique(['KodeKlinik', 'DentalUnit', 'BarangId'], 'stok_unique_per_du');
        });
    }

    public function down(): void
    {
        Schema::table('stok', function (Blueprint $table) {
            $table->dropUnique('stok_unique_per_du');
            $table->unique(['KodeKlinik', 'BarangId']);
            $table->dropColumn('DentalUnit');
        });
    }
};
