<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'tanggal_bergabung')) {
            Schema::table('users', function (Blueprint $table) {
                $table->date('tanggal_bergabung')->nullable()->after('kodeperusahaan');
            });
        }

        Schema::table('transaksis', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksis', 'IdPerawatPendamping')) {
                $table->unsignedBigInteger('IdPerawatPendamping')->nullable()->after('IdPerawat');
                $table->foreign('IdPerawatPendamping')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('transaksis', 'IdResepsionisPendamping')) {
                $table->unsignedBigInteger('IdResepsionisPendamping')->nullable()->after('IdResepsionis');
                $table->foreign('IdResepsionisPendamping')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (Schema::hasColumn('transaksis', 'IdPerawatPendamping')) {
                $table->dropForeign(['IdPerawatPendamping']);
                $table->dropColumn('IdPerawatPendamping');
            }
            if (Schema::hasColumn('transaksis', 'IdResepsionisPendamping')) {
                $table->dropForeign(['IdResepsionisPendamping']);
                $table->dropColumn('IdResepsionisPendamping');
            }
        });

        if (Schema::hasColumn('users', 'tanggal_bergabung')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('tanggal_bergabung');
            });
        }
    }
};
