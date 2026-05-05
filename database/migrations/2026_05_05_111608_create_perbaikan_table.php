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
        Schema::create('perbaikan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->string('kategori');
            $table->string('sub_kategori')->nullable();
            $table->string('type')->nullable();
            $table->integer('jumlah_diperbaiki');
            $table->string('satuan');
            $table->text('deskripsi_perbaikan');
            $table->text('keterangan')->nullable();
            $table->date('tanggal_perbaikan');
            $table->string('dilaporkan_oleh');
            $table->enum('status', ['pending', 'proses', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perbaikan');
    }
};
