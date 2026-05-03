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
        Schema::create('master_kliniks', function (Blueprint $table) {
            $table->id();
            $table->string('Kode')->nullable();
            $table->string('Nama')->nullable();
            $table->string('Alamat')->nullable();
            $table->string('NoTelp')->nullable();
            $table->string('Email')->nullable();
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
        Schema::dropIfExists('master_kliniks');
    }
};
