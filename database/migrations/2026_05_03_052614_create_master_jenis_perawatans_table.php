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
        Schema::create('master_jenis_perawatans', function (Blueprint $table) {
            $table->id();
            $table->string('Kode')->nullable();
            $table->string('Nama')->nullable();
            $table->decimal('Tarif', 15, 2)->nullable()->default(0.0);
            $table->string('KodeCabang')->nullable();
            $table->enum('Status', ['Y', 'N'])->nullable()->default('Y');
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
        Schema::dropIfExists('master_jenis_perawatans');
    }
};
