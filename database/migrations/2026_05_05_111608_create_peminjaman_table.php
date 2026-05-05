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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->string('kategori');
            $table->string('sub_kategori')->nullable();
            $table->string('type')->nullable();
            $table->integer('jumlah_dipinjam');
            $table->string('satuan');
            $table->text('peminjam');
            $table->text('keterangan')->nullable();
            $table->date('tanggal_peminjaman');
            $table->date('tanggal_kembali')->nullable();
            $table->string('dilaporkan_oleh');
            $table->enum('status', ['dipinjam', 'dikembalikan', 'terlambat'])->default('dipinjam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
