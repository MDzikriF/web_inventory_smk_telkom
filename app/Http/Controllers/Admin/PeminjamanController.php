<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class PeminjamanController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::orderBy('tanggal_peminjaman', 'desc')->paginate(10);
        return view('admin.peminjaman.index', compact('peminjaman'));
    }

    public function create()
    {
        return view('admin.peminjaman.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'sub_kategori' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'jumlah_dipinjam' => 'required|integer|min:1',
            'satuan' => 'required|string|max:255',
            'peminjam' => 'required|string',
            'keterangan' => 'nullable|string',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_kembali' => 'nullable|date',
        ]);

        Peminjaman::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'kategori' => $request->kategori,
            'sub_kategori' => $request->sub_kategori,
            'type' => $request->type,
            'jumlah_dipinjam' => $request->jumlah_dipinjam,
            'satuan' => $request->satuan,
            'peminjam' => $request->peminjam,
            'keterangan' => $request->keterangan,
            'tanggal_peminjaman' => $request->tanggal_peminjaman,
            'tanggal_kembali' => $request->tanggal_kembali,
            'dilaporkan_oleh' => Auth::user()->name ?? 'Admin',
            'status' => 'dipinjam',
        ]);

        return redirect()->route('admin.peminjaman.index')->with('success', 'Laporan peminjaman berhasil dibuat');
    }

    public function edit($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        return view('admin.peminjaman.edit', compact('peminjaman'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'sub_kategori' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'jumlah_dipinjam' => 'required|integer|min:1',
            'satuan' => 'required|string|max:255',
            'peminjam' => 'required|string',
            'keterangan' => 'nullable|string',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_kembali' => 'nullable|date',
            'status' => 'required|in:dipinjam,dikembalikan,terlambat',
        ]);

        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update($request->all());

        return redirect()->route('admin.peminjaman.index')->with('success', 'Laporan peminjaman berhasil diupdate');
    }

    public function destroy($id)
    {
        Peminjaman::findOrFail($id)->delete();
        return redirect()->route('admin.peminjaman.index')->with('success', 'Laporan peminjaman berhasil dihapus');
    }

    public function exportPdf()
    {
        $peminjaman = Peminjaman::orderBy('tanggal_peminjaman', 'desc')->get();
        $pdf = PDF::loadView('admin.peminjaman.print', compact('peminjaman'));
        return $pdf->download('Laporan_Peminjaman_Aset_' . date('Y-m-d') . '.pdf');
    }

    public function exportWord()
    {
        $peminjaman = Peminjaman::orderBy('tanggal_peminjaman', 'desc')->get();
        
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        
        $section->addText('LAPORAN PEMINJAMAN ASET', ['bold' => true, 'size' => 16]);
        $section->addText('Tanggal: ' . date('d-m-Y'));
        $section->addTextBreak();
        
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        $table->addRow();
        $table->addCell(2000)->addText('Kode Barang', ['bold' => true]);
        $table->addCell(3000)->addText('Nama Barang', ['bold' => true]);
        $table->addCell(2000)->addText('Kategori', ['bold' => true]);
        $table->addCell(1500)->addText('Jumlah', ['bold' => true]);
        $table->addCell(2000)->addText('Peminjam', ['bold' => true]);
        $table->addCell(2000)->addText('Tanggal Pinjam', ['bold' => true]);
        $table->addCell(2000)->addText('Tanggal Kembali', ['bold' => true]);
        $table->addCell(1500)->addText('Status', ['bold' => true]);
        
        foreach ($peminjaman as $item) {
            $table->addRow();
            $table->addCell(2000)->addText($item->kode_barang);
            $table->addCell(3000)->addText($item->nama_barang);
            $table->addCell(2000)->addText($item->kategori);
            $table->addCell(1500)->addText($item->jumlah_dipinjam . ' ' . $item->satuan);
            $table->addCell(2000)->addText($item->peminjam);
            $table->addCell(2000)->addText($item->tanggal_peminjaman->format('d-m-Y'));
            $table->addCell(2000)->addText($item->tanggal_kembali ? $item->tanggal_kembali->format('d-m-Y') : '-');
            $table->addCell(1500)->addText($item->status);
        }
        
        $fileName = 'Laporan_Peminjaman_Aset_' . date('Y-m-d') . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $tempPath = storage_path('app/temp/' . $fileName);
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $objWriter->save($tempPath);
        
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    public function exportExcel()
    {
        $peminjaman = Peminjaman::orderBy('tanggal_peminjaman', 'desc')->get();
        $fileName = "Laporan_Peminjaman_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use($peminjaman) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ["LAPORAN PEMINJAMAN BARANG"]);
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
