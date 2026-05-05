<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemRequest;
use App\Models\Item;
use App\Models\Peminjaman;
use App\Models\Perbaikan;
use Illuminate\Support\Facades\DB;
use PDF;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class HistoryController extends Controller
{
    public function index()
    {
        // Redirect to new unified Notification & History page
        return redirect()->route('admin.notifications.index');
    }

    public function laporan()
    {
        return view('admin.history.laporan');
    }

    public function returnItem($id)
    {
        $req = ItemRequest::with('details.item.category')->findOrFail($id);
        
        if (!in_array($req->status, ['approved', 'return_requested'])) {
            return redirect()->back()->with('error', 'Pastikan status barang sedang dipinjam atau menunggu pengembalian.');
        }

        DB::transaction(function() use ($req) {
            $req->update(['status' => 'returned', 'return_date' => now()]);
            
            // Kembalikan stok hanya untuk kategori "Hardware" dan buat record transaksi masuk
            foreach($req->details as $detail) {
                if ($detail->item && $detail->item->category->name === 'Hardware') {
                    Item::where('id', $detail->item_id)->increment('stock', $detail->quantity);
                    
                    // Buat record transaksi masuk
                    \App\Models\Transaction::create([
                        'item_id' => $detail->item_id,
                        'type' => 'in',
                        'quantity' => $detail->quantity,
                        'date' => now()->toDateString(),
                        'notes' => 'Masuk dari pengembalian - Request ID: #' . str_pad($req->id, 4, '0', STR_PAD_LEFT) . ' - User: ' . $req->user->name,
                    ]);
                }
                // Untuk kategori "sekali pakai", stok tidak dikembalikan karena habis pakai
            }
        });

        return redirect()->back()->with('success', 'Barang ditandai telah dikembalikan. Stok berhasil dipulihkan!');
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $transactions = \App\Models\Transaction::with('item')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'asc')
            ->get();

        $damageReports = \App\Models\DamageReport::with(['item', 'user'])
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'asc')
            ->get();

        $bulanNama = \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('F');

        return view('admin.history.print_report', compact('transactions', 'damageReports', 'bulan', 'tahun', 'bulanNama'));
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $transactions = \App\Models\Transaction::with('item')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'asc')
            ->get();

        $damageReports = \App\Models\DamageReport::with(['item', 'user'])
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'asc')
            ->get();

        $bulanNama = \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('F');
        $fileName = "Laporan_Bulanan_{$bulanNama}_{$tahun}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($transactions, $damageReports, $bulanNama, $tahun) {
            $file = fopen('php://output', 'w');
            
            // Laporan Keluar Masuk
            fputcsv($file, ["LAPORAN KELUAR MASUK BARANG - $bulanNama $tahun"]);
            fputcsv($file, ['Tanggal', 'Barang', 'Tipe', 'Jumlah', 'Keterangan']);
            
            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->created_at->format('Y-m-d H:i'),
                    $t->item ? $t->item->name : 'Barang Dihapus',
                    $t->type == 'in' ? 'Masuk' : 'Keluar',
                    $t->quantity,
                    $t->notes
                ]);
            }

            fputcsv($file, []); // Empty line

            // Laporan Kerusakan
            fputcsv($file, ["LAPORAN KERUSAKAN BARANG - $bulanNama $tahun"]);
            fputcsv($file, ['Tanggal', 'Pelapor', 'Barang', 'Status', 'Catatan']);
            
            foreach ($damageReports as $d) {
                fputcsv($file, [
                    $d->created_at->format('Y-m-d H:i'),
                    $d->user ? $d->user->name : 'User Dihapus',
                    $d->item ? $d->item->name : 'Barang Dihapus',
                    $d->status,
                    $d->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Export Peminjaman Aset (Separate)
    public function exportPeminjamanPdf(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $peminjaman = Peminjaman::whereMonth('tanggal_peminjaman', $bulan)
            ->whereYear('tanggal_peminjaman', $tahun)
            ->orderBy('tanggal_peminjaman', 'desc')
            ->get();

        $bulanNama = \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('F');

        return view('admin.history.print_peminjaman', compact('peminjaman', 'bulan', 'tahun', 'bulanNama'));
    }

    public function exportPeminjamanWord(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $peminjaman = Peminjaman::whereMonth('tanggal_peminjaman', $bulan)
            ->whereYear('tanggal_peminjaman', $tahun)
            ->orderBy('tanggal_peminjaman', 'desc')
            ->get();

        $bulanNama = \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('F');

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText('SMK TELKOM JAKARTA', ['bold' => true, 'size' => 16, 'alignment' => 'center']);
        $section->addText('LABORATORIUM & INVENTARIS BARANG', ['bold' => true, 'size' => 14, 'alignment' => 'center']);
        $section->addText('Jl. Daan Mogot Km. 11 Cengkareng, Jakarta Barat 11710', ['alignment' => 'center']);
        $section->addTextBreak();
        $section->addText('LAPORAN PEMINJAMAN ASET', ['bold' => true, 'size' => 14, 'alignment' => 'center']);
        $section->addText('Periode: ' . $bulanNama . ' ' . $tahun, ['alignment' => 'center']);
        $section->addTextBreak();

        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        $table->addRow();
        $table->addCell(500)->addText('No', ['bold' => true]);
        $table->addCell(2000)->addText('Kode Barang', ['bold' => true]);
        $table->addCell(3000)->addText('Nama Barang', ['bold' => true]);
        $table->addCell(2000)->addText('Kategori', ['bold' => true]);
        $table->addCell(1500)->addText('Jumlah', ['bold' => true]);
        $table->addCell(2000)->addText('Peminjam', ['bold' => true]);
        $table->addCell(2000)->addText('Tanggal Pinjam', ['bold' => true]);
        $table->addCell(2000)->addText('Tanggal Kembali', ['bold' => true]);
        $table->addCell(1500)->addText('Status', ['bold' => true]);

        foreach ($peminjaman as $index => $item) {
            $table->addRow();
            $table->addCell(500)->addText($index + 1);
            $table->addCell(2000)->addText($item->kode_barang);
            $table->addCell(3000)->addText($item->nama_barang);
            $table->addCell(2000)->addText($item->kategori);
            $table->addCell(1500)->addText($item->jumlah_dipinjam . ' ' . $item->satuan);
            $table->addCell(2000)->addText($item->peminjam);
            $table->addCell(2000)->addText($item->tanggal_peminjaman->format('d-m-Y'));
            $table->addCell(2000)->addText($item->tanggal_kembali ? $item->tanggal_kembali->format('d-m-Y') : '-');
            $table->addCell(1500)->addText($item->status);
        }

        $fileName = 'Laporan_Peminjaman_Aset_' . $bulanNama . '_' . $tahun . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $tempPath = storage_path('app/temp/' . $fileName);
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $objWriter->save($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    // Export Keluar Masuk Stok (Separate)
    public function exportStokPdf(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $transactions = \App\Models\Transaction::with('item')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'asc')
            ->get();

        $bulanNama = \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('F');

        return view('admin.history.print_stok', compact('transactions', 'bulan', 'tahun', 'bulanNama'));
    }

    public function exportStokWord(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $transactions = \App\Models\Transaction::with('item')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'asc')
            ->get();

        $bulanNama = \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('F');

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText('SMK TELKOM JAKARTA', ['bold' => true, 'size' => 16, 'alignment' => 'center']);
        $section->addText('LABORATORIUM & INVENTARIS BARANG', ['bold' => true, 'size' => 14, 'alignment' => 'center']);
        $section->addText('Jl. Daan Mogot Km. 11 Cengkareng, Jakarta Barat 11710', ['alignment' => 'center']);
        $section->addTextBreak();
        $section->addText('LAPORAN KELUAR MASUK STOK', ['bold' => true, 'size' => 14, 'alignment' => 'center']);
        $section->addText('Periode: ' . $bulanNama . ' ' . $tahun, ['alignment' => 'center']);
        $section->addTextBreak();

        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        $table->addRow();
        $table->addCell(500)->addText('No', ['bold' => true]);
        $table->addCell(2000)->addText('Tanggal', ['bold' => true]);
        $table->addCell(3000)->addText('Nama Barang', ['bold' => true]);
        $table->addCell(1500)->addText('Tipe', ['bold' => true]);
        $table->addCell(1500)->addText('Jumlah', ['bold' => true]);
        $table->addCell(3000)->addText('Keterangan', ['bold' => true]);

        foreach ($transactions as $index => $t) {
            $table->addRow();
            $table->addCell(500)->addText($index + 1);
            $table->addCell(2000)->addText($t->created_at->format('d-m-Y'));
            $table->addCell(3000)->addText($t->item ? $t->item->name : 'Barang Dihapus');
            $table->addCell(1500)->addText($t->type == 'in' ? 'Masuk' : 'Keluar');
            $table->addCell(1500)->addText($t->quantity);
            $table->addCell(3000)->addText($t->notes ?: '-');
        }

        $fileName = 'Laporan_Stok_' . $bulanNama . '_' . $tahun . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $tempPath = storage_path('app/temp/' . $fileName);
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $objWriter->save($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    // Export Laporan Perbaikan (Separate)
    public function exportPerbaikanPdf(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $perbaikan = Perbaikan::whereMonth('tanggal_perbaikan', $bulan)
            ->whereYear('tanggal_perbaikan', $tahun)
            ->orderBy('tanggal_perbaikan', 'desc')
            ->get();

        $bulanNama = \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('F');

        return view('admin.history.print_perbaikan', compact('perbaikan', 'bulan', 'tahun', 'bulanNama'));
    }

    public function exportPerbaikanWord(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $perbaikan = Perbaikan::whereMonth('tanggal_perbaikan', $bulan)
            ->whereYear('tanggal_perbaikan', $tahun)
            ->orderBy('tanggal_perbaikan', 'desc')
            ->get();

        $bulanNama = \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('F');

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText('SMK TELKOM JAKARTA', ['bold' => true, 'size' => 16, 'alignment' => 'center']);
        $section->addText('LABORATORIUM & INVENTARIS BARANG', ['bold' => true, 'size' => 14, 'alignment' => 'center']);
        $section->addText('Jl. Daan Mogot Km. 11 Cengkareng, Jakarta Barat 11710', ['alignment' => 'center']);
        $section->addTextBreak();
        $section->addText('LAPORAN PERBAIKAN', ['bold' => true, 'size' => 14, 'alignment' => 'center']);
        $section->addText('Periode: ' . $bulanNama . ' ' . $tahun, ['alignment' => 'center']);
        $section->addTextBreak();

        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        $table->addRow();
        $table->addCell(500)->addText('No', ['bold' => true]);
        $table->addCell(2000)->addText('Kode Barang', ['bold' => true]);
        $table->addCell(3000)->addText('Nama Barang', ['bold' => true]);
        $table->addCell(2000)->addText('Kategori', ['bold' => true]);
        $table->addCell(1500)->addText('Jumlah', ['bold' => true]);
        $table->addCell(3000)->addText('Deskripsi Perbaikan', ['bold' => true]);
        $table->addCell(2000)->addText('Tanggal', ['bold' => true]);
        $table->addCell(1500)->addText('Status', ['bold' => true]);

        foreach ($perbaikan as $index => $item) {
            $table->addRow();
            $table->addCell(500)->addText($index + 1);
            $table->addCell(2000)->addText($item->kode_barang);
            $table->addCell(3000)->addText($item->nama_barang);
            $table->addCell(2000)->addText($item->kategori);
            $table->addCell(1500)->addText($item->jumlah_diperbaiki . ' ' . $item->satuan);
            $table->addCell(3000)->addText($item->deskripsi_perbaikan);
            $table->addCell(2000)->addText($item->tanggal_perbaikan->format('d-m-Y'));
            $table->addCell(1500)->addText($item->status);
        }

        $fileName = 'Laporan_Perbaikan_' . $bulanNama . '_' . $tahun . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $tempPath = storage_path('app/temp/' . $fileName);
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $objWriter->save($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    public function exportPeminjamanExcel(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $peminjaman = Peminjaman::whereMonth('tanggal_peminjaman', $bulan)
            ->whereYear('tanggal_peminjaman', $tahun)
            ->orderBy('tanggal_peminjaman', 'desc')
            ->get();

        $bulanNama = \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('F');
        $fileName = "Laporan_Peminjaman_Aset_{$bulanNama}_{$tahun}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($peminjaman, $bulanNama, $tahun) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ["LAPORAN PEMINJAMAN ASET - $bulanNama $tahun"]);
            fputcsv($file, ['No', 'Kode Barang', 'Nama Barang', 'Kategori', 'Jumlah', 'Peminjam', 'Tanggal Pinjam', 'Tanggal Kembali', 'Status']);

            foreach ($peminjaman as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->kode_barang,
                    $item->nama_barang,
                    $item->kategori,
                    $item->jumlah_dipinjam . ' ' . $item->satuan,
                    $item->peminjam,
                    $item->tanggal_peminjaman->format('d-m-Y'),
                    $item->tanggal_kembali ? $item->tanggal_kembali->format('d-m-Y') : '-',
                    $item->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportStokExcel(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $transactions = \App\Models\Transaction::with('item')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'asc')
            ->get();

        $bulanNama = \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('F');
        $fileName = "Laporan_Keluar_Masuk_Stok_{$bulanNama}_{$tahun}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($transactions, $bulanNama, $tahun) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ["LAPORAN KELUAR MASUK STOK - $bulanNama $tahun"]);
            fputcsv($file, ['No', 'Tanggal', 'Nama Barang', 'Tipe', 'Jumlah', 'Keterangan']);

            foreach ($transactions as $index => $t) {
                fputcsv($file, [
                    $index + 1,
                    $t->created_at->format('d-m-Y H:i'),
                    $t->item ? $t->item->name : 'Barang Dihapus',
                    $t->type == 'in' ? 'Masuk' : 'Keluar',
                    $t->quantity,
                    $t->notes ?: '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPerbaikanExcel(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $perbaikan = Perbaikan::whereMonth('tanggal_perbaikan', $bulan)
            ->whereYear('tanggal_perbaikan', $tahun)
            ->orderBy('tanggal_perbaikan', 'desc')
            ->get();

        $bulanNama = \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('F');
        $fileName = "Laporan_Perbaikan_{$bulanNama}_{$tahun}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($perbaikan, $bulanNama, $tahun) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ["LAPORAN PERBAIKAN - $bulanNama $tahun"]);
            fputcsv($file, ['No', 'Kode Barang', 'Nama Barang', 'Kategori', 'Jumlah', 'Deskripsi Perbaikan', 'Tanggal', 'Status']);

            foreach ($perbaikan as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->kode_barang,
                    $item->nama_barang,
                    $item->kategori,
                    $item->jumlah_diperbaiki . ' ' . $item->satuan,
                    $item->deskripsi_perbaikan,
                    $item->tanggal_perbaikan->format('d-m-Y'),
                    $item->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
