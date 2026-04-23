<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanRusak extends Model
{
    use HasFactory;

    protected $table = 'laporan_rusak';
    
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
        'audit_date',
        'audit_file',
        'audit_notes',
        'reconciliation_status',
    ];

    protected $casts = [
        'tanggal_lapor' => 'date',
    ];

    public function getUser()
    {
        return $this->belongsTo(User::class, 'dilaporkan_oleh', 'nip');
    }
}
