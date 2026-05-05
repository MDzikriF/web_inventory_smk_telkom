<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            DB::statement("ALTER TABLE damage_reports MODIFY COLUMN status ENUM('pending', 'reviewed', 'resolved', 'unrepairable') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            DB::statement("ALTER TABLE damage_reports MODIFY COLUMN status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending'");
        });
    }
};
