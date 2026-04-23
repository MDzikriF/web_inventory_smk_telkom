<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->string('kategori');
            $table->string('sub_kategori')->nullable();
            $table->string('type')->nullable();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->integer('jumlah');
            $table->string('satuan');
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->string('dibuat_oleh');
            $table->timestamps();
        });

        Schema::create('laporan_rusak', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->string('kategori');
            $table->string('sub_kategori')->nullable();
            $table->string('type')->nullable();
            $table->integer('jumlah_rusak');
            $table->string('satuan');
            $table->text('kerusakan')->nullable();
            $table->text('keterangan')->nullable();
            $table->date('tanggal_lapor');
            $table->string('dilaporkan_oleh');
            $table->enum('status', ['pending', 'proses', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_barang');
        Schema::dropIfExists('laporan_rusak');
    }
};
