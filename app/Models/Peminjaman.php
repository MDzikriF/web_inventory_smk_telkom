<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'sub_kategori',
        'type',
        'jumlah_dipinjam',
        'satuan',
        'peminjam',
        'keterangan',
        'tanggal_peminjaman',
        'tanggal_kembali',
        'dilaporkan_oleh',
        'status',
    ];

    protected $casts = [
        'tanggal_peminjaman' => 'date',
        'tanggal_kembali' => 'date',
    ];
}
