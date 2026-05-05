<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kerusakan;
use App\Models\Perbaikan;
use App\Models\Peminjaman;
use App\Models\LaporanBarang;
use Illuminate\Http\Request;

class LaporanKeseluruhanController extends Controller
{
    public function index()
    {
        $laporanBarang = LaporanBarang::orderBy('tanggal', 'desc')->paginate(10);
        $kerusakan = Kerusakan::orderBy('tanggal_lapor', 'desc')->paginate(10);
        $perbaikan = Perbaikan::orderBy('tanggal_perbaikan', 'desc')->paginate(10);
        $peminjaman = Peminjaman::orderBy('tanggal_peminjaman', 'desc')->paginate(10);
        
        return view('admin.laporan_keseluruhan.index', compact('laporanBarang', 'kerusakan', 'perbaikan', 'peminjaman'));
    }

    public function exportPdf()
    {
        $laporanBarang = LaporanBarang::orderBy('tanggal', 'desc')->get();
        $kerusakan = Kerusakan::orderBy('tanggal_lapor', 'desc')->get();
        $perbaikan = Perbaikan::orderBy('tanggal_perbaikan', 'desc')->get();
        $peminjaman = Peminjaman::orderBy('tanggal_peminjaman', 'desc')->get();
        
        return view('admin.laporan_keseluruhan.print', compact('laporanBarang', 'kerusakan', 'perbaikan', 'peminjaman'));
    }

    public function exportExcel()
    {
        $laporanBarang = LaporanBarang::orderBy('tanggal', 'desc')->get();
        $kerusakan = Kerusakan::orderBy('tanggal_lapor', 'desc')->get();
        $perbaikan = Perbaikan::orderBy('tanggal_perbaikan', 'desc')->get();
        $peminjaman = Peminjaman::orderBy('tanggal_peminjaman', 'desc')->get();
        
        $fileName = "Laporan_Keseluruhan_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use($laporanBarang, $kerusakan, $perbaikan, $peminjaman) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ["LAPORAN KESELURUHAN BARANG"]);
            fputcsv($file, []);
            
            fputcsv($file, ["LAPORAN BARANG"]);
            fputcsv($file, ['Kode Barang', 'Nama Barang', 'Kategori', 'Sub Kategori', 'Type', 'Jenis', 'Jumlah', 'Satuan', 'Tanggal']);
            foreach ($laporanBarang as $item) {
                fputcsv($file, [
                    $item->kode_barang,
                    $item->nama_barang,
                    $item->kategori,
                    $item->sub_kategori,
                    $item->type,
                    $item->jenis,
                    $item->jumlah,
                    $item->satuan,
                    $item->tanggal->format('d-m-Y')
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ["LAPORAN KERUSAKAN"]);
            fputcsv($file, ['Kode Barang', 'Nama Barang', 'Kategori', 'Sub Kategori', 'Type', 'Jumlah Rusak', 'Satuan', 'Kerusakan', 'Keterangan', 'Tanggal', 'Status']);
            foreach ($kerusakan as $item) {
                fputcsv($file, [
                    $item->kode_barang,
                    $item->nama_barang,
                    $item->kategori,
                    $item->sub_kategori,
                    $item->type,
                    $item->jumlah_rusak,
                    $item->satuan,
                    $item->kerusakan,
                    $item->keterangan,
                    $item->tanggal_lapor->format('d-m-Y'),
                    $item->status
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ["LAPORAN PERBAIKAN"]);
            fputcsv($file, ['Kode Barang', 'Nama Barang', 'Kategori', 'Sub Kategori', 'Type', 'Jumlah', 'Satuan', 'Deskripsi Perbaikan', 'Keterangan', 'Tanggal', 'Status']);
            foreach ($perbaikan as $item) {
                fputcsv($file, [
                    $item->kode_barang,
                    $item->nama_barang,
                    $item->kategori,
                    $item->sub_kategori,
                    $item->type,
                    $item->jumlah_diperbaiki,
                    $item->satuan,
                    $item->deskripsi_perbaikan,
                    $item->keterangan,
                    $item->tanggal_perbaikan->format('d-m-Y'),
                    $item->status
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ["LAPORAN PEMINJAMAN"]);
            fputcsv($file, ['Kode Barang', 'Nama Barang', 'Kategori', 'Sub Kategori', 'Type', 'Jumlah', 'Satuan', 'Peminjam', 'Keterangan', 'Tanggal Pinjam', 'Tanggal Kembali', 'Status']);
            foreach ($peminjaman as $item) {
                fputcsv($file, [
                    $item->kode_barang,
                    $item->nama_barang,
                    $item->kategori,
                    $item->sub_kategori,
                    $item->type,
                    $item->jumlah_dipinjam,
                    $item->satuan,
                    $item->peminjam,
                    $item->keterangan,
                    $item->tanggal_peminjaman->format('d-m-Y'),
                    $item->tanggal_kembali ? $item->tanggal_kembali->format('d-m-Y') : '-',
                    $item->status
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
