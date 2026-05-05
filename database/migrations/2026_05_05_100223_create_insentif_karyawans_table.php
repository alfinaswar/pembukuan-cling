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
        Schema::create('insentif_karyawans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IdTransaksi');
            $table->unsignedBigInteger('UserId');
            $table->string('Role');
            $table->decimal('Nominal', 15, 2);
            $table->string('JenisRule')->nullable();
            $table->string('Keterangan')->nullable();
            $table->string('KodeCabang')->nullable();
            $table->string('UserCreate')->nullable();
            $table->string('UserUpdate')->nullable();
            $table->string('UserDelete')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insentif_karyawans');
    }
};
