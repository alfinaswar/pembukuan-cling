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
        if (!Schema::hasColumn('transaksis', 'DentalUnit')) {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->string('DentalUnit', 100)->nullable()->after('KodeCabang');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('transaksis', 'DentalUnit')) {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->dropColumn('DentalUnit');
            });
        }
    }
};
