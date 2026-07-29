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
        // Hanya jalankan jika tabel belum ada
        if (!Schema::hasTable('dental_units')) {
            Schema::create('dental_units', function (Blueprint $table) {
                $table->id();
                $table->string('Nama')->nullable();
                $table->string('Keterangan')->nullable();
                $table->string('KodeCabang')->nullable();
                $table->string('UserCreate')->nullable();
                $table->string('UserUpdate')->nullable();
                $table->string('UserDelete')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dental_units');
    }
};
