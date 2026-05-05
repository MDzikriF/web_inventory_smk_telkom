<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kerusakan extends Model
{
    use HasFactory;

    protected $table = 'kerusakan';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'sub_kategori',
        'type',
        'jumlah_rusak',
        'satuan',
        'kerusakan',
        'keterangan',
        'tanggal_lapor',
        'dilaporkan_oleh',
        'status',
    ];

    protected $casts = [
        'tanggal_lapor' => 'date',
    ];
}
