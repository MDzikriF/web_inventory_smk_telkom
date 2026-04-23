<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanBarang extends Model
{
    use HasFactory;

    protected $table = 'laporan_barang';
    
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'sub_kategori',
        'type',
        'jenis',
        'jumlah',
        'satuan',
        'keterangan',
        'tanggal',
        'dibuat_oleh',
        'audit_date',
        'audit_file',
        'audit_notes',
        'reconciliation_status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function getUser()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'nip');
    }
}
