<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemRequest;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index()
    {
        // Redirect to new unified Notification & History page
        return redirect()->route('admin.notifications.index');
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
}
