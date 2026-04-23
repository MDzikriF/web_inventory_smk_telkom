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
        if (Schema::hasTable('item_requests')) {
            // SQLite doesn't support MODIFY, so we need to recreate the table
            Schema::table('item_requests', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            
            Schema::table('item_requests', function (Blueprint $table) {
                $table->enum('status', ['pending', 'approved', 'rejected', 'returned', 'return_requested'])->default('pending');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('item_requests')) {
            // SQLite doesn't support MODIFY, so we need to recreate the table
            Schema::table('item_requests', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            
            Schema::table('item_requests', function (Blueprint $table) {
                $table->enum('status', ['pending', 'approved', 'rejected', 'returned'])->default('pending');
            });
        }
    }
};
