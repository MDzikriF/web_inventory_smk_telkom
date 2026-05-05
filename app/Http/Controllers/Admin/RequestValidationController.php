<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemRequest;
use App\Models\Item;
use App\Models\DamageReport;
use Illuminate\Support\Facades\DB;

class RequestValidationController extends Controller
{
    public function index()
    {
        $requests = ItemRequest::with(['user', 'details.item'])
            ->whereIn('status', ['pending', 'return_requested'])
            ->latest()
            ->get();

        $damageReports = DamageReport::with(['user', 'item'])
            ->whereIn('status', ['pending', 'reviewed'])
            ->latest()
            ->get();

        return view('admin.validations.index', compact('requests', 'damageReports'));
    }

    public function approve($id)
    {
        $req = ItemRequest::with('details')->findOrFail($id);
        \Log::info("Approve request {$id}, details count: " . $req->details->count());
        
        // Cek ketahanan stok
        $canApprove = true;
        foreach($req->details as $detail) {
            $item = Item::find($detail->item_id);
            \Log::info("Cek stok: detail item_id {$detail->item_id}, quantity {$detail->quantity}, item found: " . ($item ? 'yes' : 'no'));
            if (!$item || $item->stock < $detail->quantity) {
                \Log::info("Tidak bisa approve: item " . ($item ? "stok {$item->stock} < {$detail->quantity}" : "item not found"));
                $canApprove = false;
                break;
            }
        }
        
        if(!$canApprove) {
            return redirect()->back()->with('error', 'Stok Barang tidak mencukupi untuk memenuhi permintaan ini.');
        }

        DB::transaction(function() use ($req) {
            $req->update(['status' => 'approved']);
            // Kurangi stok di inventory dan buat record transaksi
            foreach($req->details as $detail) {
                $item = Item::find($detail->item_id);
                if ($item) {
                    $oldStock = $item->stock;
                    $item->decrement('stock', $detail->quantity);
                    \Log::info("Stok berkurang: Item {$item->id} dari {$oldStock} ke {$item->stock}");
                    
                    // Buat record transaksi keluar
                    \App\Models\Transaction::create([
                        'item_id' => $detail->item_id,
                        'type' => 'out',
                        'quantity' => $detail->quantity,
                        'date' => now()->toDateString(),
                        'notes' => 'Keluar untuk pinjaman - Request ID: #' . str_pad($req->id, 4, '0', STR_PAD_LEFT) . ' - User: ' . $req->user->name,
                    ]);
                } else {
                    \Log::error("Item tidak ditemukan: {$detail->item_id}");
                }
            }
            
            \App\Models\Notification::create([
                'user_id' => $req->user_id,
                'title' => 'Permintaan Disetujui! ✅',
                'message' => 'Permintaan pinjaman (ID: #'.str_pad($req->id, 4, '0', STR_PAD_LEFT).') telah DISETUJUI oleh Admin. Silakan ambil di ruang Lab.',
            ]);
        });

        return redirect()->back()->with('success', 'Permintaan disetujui! Stok inventaris telah dikurangi.');
    }

    public function reject($id)
    {
        $req = ItemRequest::findOrFail($id);
        $req->update(['status' => 'rejected']);
        
        \App\Models\Notification::create([
            'user_id' => $req->user_id,
            'title' => 'Permintaan Ditolak 🚫',
            'message' => 'Mohon maaf, permintaan pinjaman (ID: #'.str_pad($req->id, 4, '0', STR_PAD_LEFT).') DITOLAK oleh Admin. Hubungi petugas Lab.',
        ]);
        
        return redirect()->back()->with('success', 'Permintaan telah ditolak.');
    }

    public function unrepairableDamage($id)
    {
        $report = DamageReport::findOrFail($id);
        
        DB::transaction(function() use ($report) {
            $report->update(['status' => 'unrepairable']);

            // Kurangi stok barang jika masih ada (karena rusak permanen)
            if ($report->item && $report->item->stock > 0) {
                $report->item->decrement('stock', 1);

                // Catat transaksi barang rusak
                \App\Models\Transaction::create([
                    'item_id' => $report->item_id,
                    'type' => 'out',
                    'quantity' => 1,
                    'date' => now()->toDateString(),
                    'notes' => 'Penyesuaian stok: Barang rusak permanen (Laporan #' . str_pad($report->id, 4, '0', STR_PAD_LEFT) . ')',
                ]);
            }

            \App\Models\Notification::create([
                'user_id' => $report->user_id,
                'title' => 'Laporan Kerusakan - Barang Rusak ⚠️',
                'message' => 'Mohon maaf, barang "'.$report->item->name.'" yang Anda laporkan mengalami kerusakan dan tidak bisa diperbaiki.',
            ]);
        });

        return redirect()->back()->with('success', 'Barang ditandai rusak permanen dan stok telah dikurangi.');
    }

    public function resolveDamage($id)
    {
        $report = DamageReport::findOrFail($id);
        $report->update(['status' => 'resolved']);

        \App\Models\Notification::create([
            'user_id' => $report->user_id,
            'title' => 'Laporan Kerusakan - Selesai Diperbaiki ✅',
            'message' => 'Barang "'.$report->item->name.'" yang Anda laporkan telah selesai diperbaiki dan bisa digunakan kembali.',
        ]);

        return redirect()->back()->with('success', 'Barang ditandai telah selesai diperbaiki.');
    }
}
