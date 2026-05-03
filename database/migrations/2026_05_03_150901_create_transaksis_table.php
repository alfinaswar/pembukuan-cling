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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('Kode')->nullable();
            $table->date('Tanggal')->nullable();
            $table->string('NamaPasien')->nullable();
            $table->enum('JenisPasien', ['Baru', 'Lama'])->nullable();
            $table->string('MetodePembayaran')->nullable();
            $table->decimal('BiayaAdmin', 15, 2)->nullable()->default(0.0);
            $table->decimal('TotalBayar', 15, 2)->nullable()->default(0.0);
            $table->string('IdResepsionis')->nullable();
            $table->string('IdPerawat')->nullable();
            $table->string('IdDokter')->nullable();
            $table->string('Shift')->nullable();
            $table->string('UserCreate')->nullable();
            $table->string('UserUpdate')->nullable();
            $table->string('UserDelete')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
