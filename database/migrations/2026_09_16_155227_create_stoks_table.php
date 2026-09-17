<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('stok')) {
            return;
        }

        Schema::create('stok', function (Blueprint $table) {
            $table->id();
            $table->string('KodeKlinik');
            $table->string('BarangId');
            $table->integer('StokAkhir')->default(0);
            $table->string('UserCreate')->nullable();
            $table->string('UserUpdate')->nullable();
            $table->timestamps();
            $table->unique(['KodeKlinik', 'BarangId']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok');
    }
};
