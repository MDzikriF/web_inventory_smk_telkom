<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('kode_barang')->nullable()->after('name');
            $table->string('type')->nullable()->after('kode_barang');
            $table->string('sub_kategori')->nullable()->after('category_id'); // KBM atau Khusus
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['kode_barang', 'type', 'sub_kategori']);
        });
    }
};
