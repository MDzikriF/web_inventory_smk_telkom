<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanBarang;
use App\Models\LaporanRusak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}