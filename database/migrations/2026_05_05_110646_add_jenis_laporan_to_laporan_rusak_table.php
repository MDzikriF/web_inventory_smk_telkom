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
        Schema::table('laporan_rusak', function (Blueprint $table) {
            $table->enum('jenis_laporan', ['kerusakan', 'perbaikan', 'peminjaman'])->default('kerusakan')->after('kerusakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_rusak', function (Blueprint $table) {
            $table->dropColumn('jenis_laporan');
        });
    }
};
