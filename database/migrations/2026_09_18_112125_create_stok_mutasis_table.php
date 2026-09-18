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
        Schema::create('stok_mutasi', function (Blueprint $table) {
            $table->id();
            $table->string('KodeKlinik');
            $table->unsignedBigInteger('BarangId');
            $table->string('JenisMutasi', 50);
            $table->integer('Jumlah');
            $table->integer('StokSebelum')->default(0);
            $table->integer('StokSesudah')->default(0);
            $table->text('Keterangan')->nullable();
            $table->string('UserCreate')->nullable();
            $table->timestamps();
            $table->index(['KodeKlinik', 'BarangId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_mutasi');
    }
};
