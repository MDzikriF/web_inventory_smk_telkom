<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perbaikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class PerbaikanController extends Controller
{
    public function index()
    {
        $perbaikan = Perbaikan::orderBy('tanggal_perbaikan', 'desc')->paginate(10);
        return view('admin.perbaikan.index', compact('perbaikan'));
    }

    public function create()
    {
        return view('admin.perbaikan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'sub_kategori' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'jumlah_diperbaiki' => 'required|integer|min:1',
            'satuan' => 'required|string|max:255',
            'deskripsi_perbaikan' => 'required|string',
            'keterangan' => 'nullable|string',
            'tanggal_perbaikan' => 'required|date',
        ]);

        Perbaikan::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'kategori' => $request->kategori,
            'sub_kategori' => $request->sub_kategori,
            'type' => $request->type,
            'jumlah_diperbaiki' => $request->jumlah_diperbaiki,
            'satuan' => $request->satuan,
            'deskripsi_perbaikan' => $request->deskripsi_perbaikan,
            'keterangan' => $request->keterangan,
            'tanggal_perbaikan' => $request->tanggal_perbaikan,
            'dilaporkan_oleh' => Auth::user()->name ?? 'Admin',
            'status' => 'pending',
        ]);

        return redirect()->route('admin.perbaikan.index')->with('success', 'Laporan perbaikan berhasil dibuat');
    }

    public function edit($id)
    {
        $perbaikan = Perbaikan::findOrFail($id);
        return view('admin.perbaikan.edit', compact('perbaikan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'sub_kategori' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'jumlah_diperbaiki' => 'required|integer|min:1',
            'satuan' => 'required|string|max:255',
            'deskripsi_perbaikan' => 'required|string',
            'keterangan' => 'nullable|string',
            'tanggal_perbaikan' => 'required|date',
            'status' => 'required|in:pending,proses,selesai',
        ]);

        $perbaikan = Perbaikan::findOrFail($id);
        $perbaikan->update($request->all());

        return redirect()->route('admin.perbaikan.index')->with('success', 'Laporan perbaikan berhasil diupdate');
    }

    public function destroy($id)
    {
        Perbaikan::findOrFail($id)->delete();
        return redirect()->route('admin.perbaikan.index')->with('success', 'Laporan perbaikan berhasil dihapus');
    }

    public function exportPdf()
    {
        $perbaikan = Perbaikan::orderBy('tanggal_perbaikan', 'desc')->get();
        $pdf = PDF::loadView('admin.perbaikan.print', compact('perbaikan'));
        return $pdf->download('Laporan_Perbaikan_' . date('Y-m-d') . '.pdf');
    }

    public function exportWord()
    {
        $perbaikan = Perbaikan::orderBy('tanggal_perbaikan', 'desc')->get();
        
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
        
        foreach ($perbaikan as $item) {
            $table->addRow();
            $table->addCell(2000)->addText($item->kode_barang);
            $table->addCell(3000)->addText($item->nama_barang);
            $table->addCell(2000)->addText($item->kategori);
            $table->addCell(1500)->addText($item->jumlah_diperbaiki . ' ' . $item->satuan);
            $table->addCell(3000)->addText($item->deskripsi_perbaikan);
            $table->addCell(2000)->addText($item->tanggal_perbaikan->format('d-m-Y'));
            $table->addCell(1500)->addText($item->status);
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

    public function exportExcel()
    {
        $perbaikan = Perbaikan::orderBy('tanggal_perbaikan', 'desc')->get();
        $fileName = "Laporan_Perbaikan_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use($perbaikan) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ["LAPORAN PERBAIKAN BARANG"]);
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
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
