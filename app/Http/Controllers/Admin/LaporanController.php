<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanBarang;
use App\Models\LaporanRusak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $laporanBarang = LaporanBarang::orderBy('tanggal', 'desc')->paginate(10);
        $laporanRusak = LaporanRusak::orderBy('tanggal_lapor', 'desc')->paginate(10);
        
        return view('admin.laporan.index', compact('laporanBarang', 'laporanRusak'));
    }

    /**
     * Show the form for creating a new laporan barang.
     */
    public function createBarang()
    {
        return view('admin.laporan.create_barang');
    }

    /**
     * Store a newly created laporan barang.
     */
    public function storeBarang(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'sub_kategori' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date',
        ]);

        LaporanBarang::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'kategori' => $request->kategori,
            'sub_kategori' => $request->sub_kategori,
            'type' => $request->type,
            'jenis' => $request->jenis,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
            'dibuat_oleh' => Auth::user()->name ?? 'Admin',
        ]);

        return redirect()->route('admin.laporan.index')->with('success', 'Laporan barang berhasil dibuat');
    }

    /**
     * Show the form for creating a new laporan rusak.
     */
    public function createRusak()
    {
        return view('admin.laporan.create_rusak');
    }

    /**
     * Store a newly created laporan rusak.
     */
    public function storeRusak(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'sub_kategori' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'jumlah_rusak' => 'required|integer|min:1',
            'satuan' => 'required|string|max:255',
            'kerusakan' => 'required|string',
            'keterangan' => 'nullable|string',
            'tanggal_lapor' => 'required|date',
        ]);

        LaporanRusak::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'kategori' => $request->kategori,
            'sub_kategori' => $request->sub_kategori,
            'type' => $request->type,
            'jumlah_rusak' => $request->jumlah_rusak,
            'satuan' => $request->satuan,
            'kerusakan' => $request->kerusakan,
            'keterangan' => $request->keterangan,
            'tanggal_lapor' => $request->tanggal_lapor,
            'dilaporkan_oleh' => Auth::user()->name ?? 'Admin',
            'status' => 'pending',
        ]);

        return redirect()->route('admin.laporan.index')->with('success', 'Laporan rusak berhasil dibuat');
    }

    // Export Barang Masuk (Stok Masuk)
    public function exportBarangMasukPdf()
    {
        $laporanBarangMasuk = LaporanBarang::where('jenis', 'masuk')->orderBy('tanggal', 'desc')->get();
        $pdf = PDF::loadView('admin.laporan.print_barang_masuk', compact('laporanBarangMasuk'));
        return $pdf->download('Laporan_Stok_Masuk_' . date('Y-m-d') . '.pdf');
    }

    public function exportBarangMasukWord()
    {
        $laporanBarangMasuk = LaporanBarang::where('jenis', 'masuk')->orderBy('tanggal', 'desc')->get();
        
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        
        $section->addText('LAPORAN STOK MASUK', ['bold' => true, 'size' => 16]);
        $section->addText('Tanggal: ' . date('d-m-Y'));
        $section->addTextBreak();
        
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        $table->addRow();
        $table->addCell(2000)->addText('Kode Barang', ['bold' => true]);
        $table->addCell(3000)->addText('Nama Barang', ['bold' => true]);
        $table->addCell(2000)->addText('Kategori', ['bold' => true]);
        $table->addCell(1500)->addText('Jumlah', ['bold' => true]);
        $table->addCell(2000)->addText('Satuan', ['bold' => true]);
        $table->addCell(3000)->addText('Keterangan', ['bold' => true]);
        $table->addCell(2000)->addText('Tanggal', ['bold' => true]);
        
        foreach ($laporanBarangMasuk as $laporan) {
            $table->addRow();
            $table->addCell(2000)->addText($laporan->kode_barang);
            $table->addCell(3000)->addText($laporan->nama_barang);
            $table->addCell(2000)->addText($laporan->kategori);
            $table->addCell(1500)->addText($laporan->jumlah);
            $table->addCell(2000)->addText($laporan->satuan);
            $table->addCell(3000)->addText($laporan->keterangan);
            $table->addCell(2000)->addText($laporan->tanggal->format('d-m-Y'));
        }
        
        $fileName = 'Laporan_Stok_Masuk_' . date('Y-m-d') . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $tempPath = storage_path('app/temp/' . $fileName);
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $objWriter->save($tempPath);
        
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    // Export Barang Keluar (Stok Keluar)
    public function exportBarangKeluarPdf()
    {
        $laporanBarangKeluar = LaporanBarang::where('jenis', 'keluar')->orderBy('tanggal', 'desc')->get();
        $pdf = PDF::loadView('admin.laporan.print_barang_keluar', compact('laporanBarangKeluar'));
        return $pdf->download('Laporan_Stok_Keluar_' . date('Y-m-d') . '.pdf');
    }

    public function exportBarangKeluarWord()
    {
        $laporanBarangKeluar = LaporanBarang::where('jenis', 'keluar')->orderBy('tanggal', 'desc')->get();
        
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        
        $section->addText('LAPORAN STOK KELUAR', ['bold' => true, 'size' => 16]);
        $section->addText('Tanggal: ' . date('d-m-Y'));
        $section->addTextBreak();
        
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        $table->addRow();
        $table->addCell(2000)->addText('Kode Barang', ['bold' => true]);
        $table->addCell(3000)->addText('Nama Barang', ['bold' => true]);
        $table->addCell(2000)->addText('Kategori', ['bold' => true]);
        $table->addCell(1500)->addText('Jumlah', ['bold' => true]);
        $table->addCell(2000)->addText('Satuan', ['bold' => true]);
        $table->addCell(3000)->addText('Keterangan', ['bold' => true]);
        $table->addCell(2000)->addText('Tanggal', ['bold' => true]);
        
        foreach ($laporanBarangKeluar as $laporan) {
            $table->addRow();
            $table->addCell(2000)->addText($laporan->kode_barang);
            $table->addCell(3000)->addText($laporan->nama_barang);
            $table->addCell(2000)->addText($laporan->kategori);
            $table->addCell(1500)->addText($laporan->jumlah);
            $table->addCell(2000)->addText($laporan->satuan);
            $table->addCell(3000)->addText($laporan->keterangan);
            $table->addCell(2000)->addText($laporan->tanggal->format('d-m-Y'));
        }
        
        $fileName = 'Laporan_Stok_Keluar_' . date('Y-m-d') . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $tempPath = storage_path('app/temp/' . $fileName);
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $objWriter->save($tempPath);
        
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    // Export Laporan Perbaikan
    public function exportPerbaikanPdf()
    {
        $laporanPerbaikan = \App\Models\DamageReport::with(['item.category', 'item.unit'])
            ->where('status', 'resolved')
            ->get()->map(function($report) {
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
                    'tanggal_lapor' => \Carbon\Carbon::parse($report->updated_at),
                    'status' => 'Selesai',
                ];
            });

        $pdf = PDF::loadView('admin.laporan.print_perbaikan', compact('laporanPerbaikan'));
        return $pdf->download('Laporan_Perbaikan_' . date('Y-m-d') . '.pdf');
    }

    public function exportPerbaikanWord()
    {
        $laporanPerbaikan = \App\Models\DamageReport::with(['item.category', 'item.unit'])
            ->where('status', 'resolved')
            ->get()->map(function($report) {
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
                    'tanggal_lapor' => \Carbon\Carbon::parse($report->updated_at),
                    'status' => 'Selesai',
                ];
            });
        
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        
        $section->addText('LAPORAN PERBAIKAN', ['bold' => true, 'size' => 16]);
        $section->addText('Tanggal: ' . date('d-m-Y'));
        $section->addTextBreak();
        
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        $table->addRow();
        $table->addCell(2000)->addText('Kode Barang', ['bold' => true]);
        $table->addCell(3000)->addText('Nama Barang', ['bold' => true]);
        $table->addCell(2000)->addText('Kategori', ['bold' => true]);
        $table->addCell(1500)->addText('Jumlah', ['bold' => true]);
        $table->addCell(3000)->addText('Deskripsi Perbaikan', ['bold' => true]);
        $table->addCell(2000)->addText('Tanggal', ['bold' => true]);
        $table->addCell(1500)->addText('Status', ['bold' => true]);
        
        foreach ($laporanPerbaikan as $laporan) {
            $table->addRow();
            $table->addCell(2000)->addText($laporan->kode_barang);
            $table->addCell(3000)->addText($laporan->nama_barang);
            $table->addCell(2000)->addText($laporan->kategori);
            $table->addCell(1500)->addText($laporan->jumlah_rusak . ' ' . $laporan->satuan);
            $table->addCell(3000)->addText($laporan->kerusakan);
            $table->addCell(2000)->addText($laporan->tanggal_lapor->format('d-m-Y'));
            $table->addCell(1500)->addText($laporan->status);
        }
        
        $fileName = 'Laporan_Perbaikan_' . date('Y-m-d') . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $tempPath = storage_path('app/temp/' . $fileName);
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $objWriter->save($tempPath);
        
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}