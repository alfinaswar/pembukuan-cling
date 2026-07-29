<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('insentif_karyawans', 'DentalUnit')) {
            Schema::table('insentif_karyawans', function (Blueprint $table) {
                $table->string('DentalUnit')->nullable()->after('KodeCabang');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('insentif_karyawans', 'DentalUnit')) {
            Schema::table('insentif_karyawans', function (Blueprint $table) {
                $table->dropColumn('DentalUnit');
            });
        }
    }
};
