<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perbaikan extends Model
{
    use HasFactory;

    protected $table = 'perbaikan';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'sub_kategori',
        'type',
        'jumlah_diperbaiki',
        'satuan',
        'deskripsi_perbaikan',
        'keterangan',
        'tanggal_perbaikan',
        'dilaporkan_oleh',
        'status',
    ];

    protected $casts = [
        'tanggal_perbaikan' => 'date',
    ];
}
