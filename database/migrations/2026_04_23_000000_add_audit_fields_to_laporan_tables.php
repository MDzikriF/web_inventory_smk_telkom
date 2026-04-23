<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_barang', function (Blueprint $table) {
            $table->date('audit_date')->nullable();
            $table->string('audit_file')->nullable();
            $table->text('audit_notes')->nullable();
            $table->enum('reconciliation_status', ['pending', 'completed', 'adjusted'])->default('pending');
        });

        Schema::table('laporan_rusak', function (Blueprint $table) {
            $table->date('audit_date')->nullable();
            $table->string('audit_file')->nullable();
            $table->text('audit_notes')->nullable();
            $table->enum('reconciliation_status', ['pending', 'completed', 'adjusted'])->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_barang', function (Blueprint $table) {
            $table->dropColumn(['audit_date', 'audit_file', 'audit_notes', 'reconciliation_status']);
        });

        Schema::table('laporan_rusak', function (Blueprint $table) {
            $table->dropColumn(['audit_date', 'audit_file', 'audit_notes', 'reconciliation_status']);
        });
    }
};