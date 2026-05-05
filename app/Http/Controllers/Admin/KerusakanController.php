<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kerusakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KerusakanController extends Controller
{
    public function index()
    {
        $kerusakan = Kerusakan::orderBy('tanggal_lapor', 'desc')->paginate(10);
        return view('admin.kerusakan.index', compact('kerusakan'));
    }

    public function create()
    {
        return view('admin.kerusakan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'sub_kategori' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'jumlah_rusak' => 'required|integer|min:1',
            'satuan' => 'required|string|max:255',
            'kerusakan' => 'required|string',
            'keterangan' => 'nullable|string',
            'tanggal_lapor' => 'required|date',
        ]);

        Kerusakan::create([
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

        return redirect()->route('admin.kerusakan.index')->with('success', 'Laporan kerusakan berhasil dibuat');
    }

    public function edit($id)
    {
        $kerusakan = Kerusakan::findOrFail($id);
        return view('admin.kerusakan.edit', compact('kerusakan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'sub_kategori' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'jumlah_rusak' => 'required|integer|min:1',
            'satuan' => 'required|string|max:255',
            'kerusakan' => 'required|string',
            'keterangan' => 'nullable|string',
            'tanggal_lapor' => 'required|date',
            'status' => 'required|in:pending,proses,selesai',
        ]);

        $kerusakan = Kerusakan::findOrFail($id);
        $kerusakan->update($request->all());

        return redirect()->route('admin.kerusakan.index')->with('success', 'Laporan kerusakan berhasil diupdate');
    }

    public function destroy($id)
    {
        Kerusakan::findOrFail($id)->delete();
        return redirect()->route('admin.kerusakan.index')->with('success', 'Laporan kerusakan berhasil dihapus');
    }

    public function exportPdf()
    {
        $kerusakan = Kerusakan::orderBy('tanggal_lapor', 'desc')->get();
        return view('admin.kerusakan.print', compact('kerusakan'));
    }

    public function exportExcel()
    {
        $kerusakan = Kerusakan::orderBy('tanggal_lapor', 'desc')->get();
        $fileName = "Laporan_Kerusakan_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use($kerusakan) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ["LAPORAN KERUSAKAN BARANG"]);
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
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
