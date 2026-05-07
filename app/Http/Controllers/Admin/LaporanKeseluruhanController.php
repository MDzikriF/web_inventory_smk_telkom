<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanKeseluruhanController extends Controller
{
    public function index()
    {
        $laporanBarang = \App\Models\Item::with(['category', 'unit'])
            ->latest()
            ->paginate(10)
            ->through(function($item) {
                return (object) [
                    'kode_barang' => $item->kode_barang ?? '-',
                    'nama_barang' => $item->name ?? '-',
                    'kategori' => $item->category->name ?? '-',
                    'sub_kategori' => $item->sub_kategori ?? '-',
                    'type' => $item->type ?? '-',
                    'jenis' => 'Aset',
                    'jumlah' => $item->stock,
                    'satuan' => $item->unit->name ?? 'Unit',
                    'tanggal' => \Carbon\Carbon::parse($item->created_at),
                ];
            });

        $kerusakan = \App\Models\DamageReport::with(['item.category', 'item.unit'])
            ->latest()
            ->paginate(10)
            ->through(function($report) {
                return (object) [
                    'kode_barang' => $report->item->kode_barang ?? '-',
                    'nama_barang' => $report->item->name ?? '-',
                    'kategori' => $report->item->category->name ?? '-',
                    'sub_kategori' => $report->item->sub_kategori ?? '-',
                    'type' => $report->item->type ?? '-',
                    'jumlah_rusak' => 1,
                    'satuan' => $report->item->unit->name ?? 'Unit',
                    'kerusakan' => $report->notes,
                    'keterangan' => 'Dilaporkan oleh ' . ($report->user->name ?? $report->reporter_name ?? 'User'),
                    'tanggal_lapor' => \Carbon\Carbon::parse($report->created_at),
                    'status' => $report->status,
                ];
            });
        
        $perbaikan = \App\Models\DamageReport::with(['item.category', 'item.unit'])
            ->where('status', 'resolved')
            ->latest()
            ->paginate(10)
            ->through(function($report) {
                return (object) [
                    'kode_barang' => $report->item->kode_barang ?? '-',
                    'nama_barang' => $report->item->name ?? '-',
                    'kategori' => $report->item->category->name ?? '-',
                    'sub_kategori' => $report->item->sub_kategori ?? '-',
                    'type' => $report->item->type ?? '-',
                    'jumlah_diperbaiki' => 1,
                    'satuan' => $report->item->unit->name ?? 'Unit',
                    'deskripsi_perbaikan' => $report->notes,
                    'keterangan' => 'Selesai diperbaiki',
                    'tanggal_perbaikan' => \Carbon\Carbon::parse($report->updated_at),
                    'status' => 'selesai',
                ];
            });

        $peminjaman = \App\Models\ItemRequestDetail::with(['itemRequest.user', 'item.category', 'item.unit'])
            ->whereHas('itemRequest', function($q) {
                $q->whereNotIn('status', ['pending', 'rejected']);
            })
            ->latest()
            ->paginate(10)
            ->through(function($detail) {
                return (object) [
                    'kode_barang' => $detail->item->kode_barang ?? '-',
                    'nama_barang' => $detail->item->name ?? '-',
                    'kategori' => $detail->item->category->name ?? '-',
                    'sub_kategori' => $detail->item->sub_kategori ?? '-',
                    'type' => $detail->item->type ?? '-',
                    'jumlah_dipinjam' => $detail->quantity,
                    'satuan' => $detail->item->unit->name ?? 'Unit',
                    'peminjam' => optional($detail->itemRequest->user)->name ?? $detail->itemRequest->reporter_name ?? 'User',
                    'keterangan' => $detail->itemRequest->notes ?? '-',
                    'tanggal_peminjaman' => \Carbon\Carbon::parse($detail->itemRequest->request_date),
                    'tanggal_kembali' => $detail->itemRequest->return_date ? \Carbon\Carbon::parse($detail->itemRequest->return_date) : null,
                    'status' => $detail->itemRequest->status,
                ];
            });
        
        return view('admin.laporan_keseluruhan.index', compact('laporanBarang', 'kerusakan', 'perbaikan', 'peminjaman'));
    }

    public function exportPdf()
    {
        $laporanBarang = \App\Models\Item::with(['category', 'unit'])->latest()->get()->map(function($item) {
            return (object) [
                'kode_barang' => $item->kode_barang ?? '-',
                'nama_barang' => $item->name ?? '-',
                'kategori' => $item->category->name ?? '-',
                'sub_kategori' => $item->sub_kategori ?? '-',
                'type' => $item->type ?? '-',
                'jenis' => 'Aset',
                'jumlah' => $item->stock,
                'satuan' => $item->unit->name ?? 'Unit',
                'tanggal' => \Carbon\Carbon::parse($item->created_at),
            ];
        });

        $kerusakan = \App\Models\DamageReport::with(['item.category', 'item.unit'])->latest()->get()->map(function($report) {
            return (object) [
                'kode_barang' => $report->item->kode_barang ?? '-',
                'nama_barang' => $report->item->name ?? '-',
                'kategori' => $report->item->category->name ?? '-',
                'sub_kategori' => $report->item->sub_kategori ?? '-',
                'type' => $report->item->type ?? '-',
                'jumlah_rusak' => 1,
                'satuan' => $report->item->unit->name ?? 'Unit',
                'kerusakan' => $report->notes,
                'keterangan' => 'Dilaporkan oleh ' . ($report->user->name ?? $report->reporter_name ?? 'User'),
                'tanggal_lapor' => \Carbon\Carbon::parse($report->created_at),
                'status' => $report->status,
            ];
        });
        
        $perbaikan = \App\Models\DamageReport::with(['item.category', 'item.unit'])
            ->where('status', 'resolved')
            ->latest()
            ->get()->map(function($report) {
                return (object) [
                    'kode_barang' => $report->item->kode_barang ?? '-',
                    'nama_barang' => $report->item->name ?? '-',
                    'kategori' => $report->item->category->name ?? '-',
                    'sub_kategori' => $report->item->sub_kategori ?? '-',
                    'type' => $report->item->type ?? '-',
                    'jumlah_diperbaiki' => 1,
                    'satuan' => $report->item->unit->name ?? 'Unit',
                    'deskripsi_perbaikan' => $report->notes,
                    'keterangan' => 'Selesai diperbaiki',
                    'tanggal_perbaikan' => \Carbon\Carbon::parse($report->updated_at),
                    'status' => 'selesai',
                ];
            });

        $peminjaman = \App\Models\ItemRequestDetail::with(['itemRequest.user', 'item.category', 'item.unit'])
            ->whereHas('itemRequest', function($q) {
                $q->whereNotIn('status', ['pending', 'rejected']);
            })
            ->latest()
            ->get()->map(function($detail) {
                return (object) [
                    'kode_barang' => $detail->item->kode_barang ?? '-',
                    'nama_barang' => $detail->item->name ?? '-',
                    'kategori' => $detail->item->category->name ?? '-',
                    'sub_kategori' => $detail->item->sub_kategori ?? '-',
                    'type' => $detail->item->type ?? '-',
                    'jumlah_dipinjam' => $detail->quantity,
                    'satuan' => $detail->item->unit->name ?? 'Unit',
                    'peminjam' => optional($detail->itemRequest->user)->name ?? $detail->itemRequest->reporter_name ?? 'User',
                    'keterangan' => $detail->itemRequest->notes ?? '-',
                    'tanggal_peminjaman' => \Carbon\Carbon::parse($detail->itemRequest->request_date),
                    'tanggal_kembali' => $detail->itemRequest->return_date ? \Carbon\Carbon::parse($detail->itemRequest->return_date) : null,
                    'status' => $detail->itemRequest->status,
                ];
            });
        
        return view('admin.laporan_keseluruhan.print', compact('laporanBarang', 'kerusakan', 'perbaikan', 'peminjaman'));
    }

    public function exportExcel()
    {
        $laporanBarang = \App\Models\Item::with(['category', 'unit'])->latest()->get()->map(function($item) {
            return (object) [
                'kode_barang' => $item->kode_barang ?? '-',
                'nama_barang' => $item->name ?? '-',
                'kategori' => $item->category->name ?? '-',
                'sub_kategori' => $item->sub_kategori ?? '-',
                'type' => $item->type ?? '-',
                'jenis' => 'Aset',
                'jumlah' => $item->stock,
                'satuan' => $item->unit->name ?? 'Unit',
                'tanggal' => \Carbon\Carbon::parse($item->created_at),
            ];
        });

        $kerusakan = \App\Models\DamageReport::with(['item.category', 'item.unit'])->latest()->get()->map(function($report) {
            return (object) [
                'kode_barang' => $report->item->kode_barang ?? '-',
                'nama_barang' => $report->item->name ?? '-',
                'kategori' => $report->item->category->name ?? '-',
                'sub_kategori' => $report->item->sub_kategori ?? '-',
                'type' => $report->item->type ?? '-',
                'jumlah_rusak' => 1,
                'satuan' => $report->item->unit->name ?? 'Unit',
                'kerusakan' => $report->notes,
                'keterangan' => 'Dilaporkan oleh ' . ($report->user->name ?? $report->reporter_name ?? 'User'),
                'tanggal_lapor' => \Carbon\Carbon::parse($report->created_at),
                'status' => $report->status,
            ];
        });
        
        $perbaikan = \App\Models\DamageReport::with(['item.category', 'item.unit'])
            ->where('status', 'resolved')
            ->latest()
            ->get()->map(function($report) {
                return (object) [
                    'kode_barang' => $report->item->kode_barang ?? '-',
                    'nama_barang' => $report->item->name ?? '-',
                    'kategori' => $report->item->category->name ?? '-',
                    'sub_kategori' => $report->item->sub_kategori ?? '-',
                    'type' => $report->item->type ?? '-',
                    'jumlah_diperbaiki' => 1,
                    'satuan' => $report->item->unit->name ?? 'Unit',
                    'deskripsi_perbaikan' => $report->notes,
                    'keterangan' => 'Selesai diperbaiki',
                    'tanggal_perbaikan' => \Carbon\Carbon::parse($report->updated_at),
                    'status' => 'selesai',
                ];
            });

        $peminjaman = \App\Models\ItemRequestDetail::with(['itemRequest.user', 'item.category', 'item.unit'])
            ->whereHas('itemRequest', function($q) {
                $q->whereNotIn('status', ['pending', 'rejected']);
            })
            ->latest()
            ->get()->map(function($detail) {
                return (object) [
                    'kode_barang' => $detail->item->kode_barang ?? '-',
                    'nama_barang' => $detail->item->name ?? '-',
                    'kategori' => $detail->item->category->name ?? '-',
                    'sub_kategori' => $detail->item->sub_kategori ?? '-',
                    'type' => $detail->item->type ?? '-',
                    'jumlah_dipinjam' => $detail->quantity,
                    'satuan' => $detail->item->unit->name ?? 'Unit',
                    'peminjam' => optional($detail->itemRequest->user)->name ?? $detail->itemRequest->reporter_name ?? 'User',
                    'keterangan' => $detail->itemRequest->notes ?? '-',
                    'tanggal_peminjaman' => \Carbon\Carbon::parse($detail->itemRequest->request_date),
                    'tanggal_kembali' => $detail->itemRequest->return_date ? \Carbon\Carbon::parse($detail->itemRequest->return_date) : null,
                    'status' => $detail->itemRequest->status,
                ];
            });
        
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
