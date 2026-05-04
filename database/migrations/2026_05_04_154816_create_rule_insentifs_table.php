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
        Schema::create('rule_insentifs', function (Blueprint $table) {
            $table->id();
            $table->string('Role')->nullable();
            $table->string('JenisRule')->nullable();
            $table->string('Operator')->nullable();
            $table->string('Nilai')->nullable();
            $table->decimal('Nominal', 15, 2)->nullable();
            $table->enum('TipeNominal', ['fixed', 'persen'])->default('fixed')->nullable();
            $table->enum('BerlakuPer', ['shift', 'transaksi'])->default('transaksi')->nullable();
            $table->json('KondisiTambahan')->nullable();
            $table->text('Keterangan')->nullable();
            $table->boolean('Status')->default(1)->nullable();
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
        Schema::dropIfExists('rule_insentifs');
    }
};
