<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'return_date' => 'nullable|date|after_or_equal:tanggal',
        ]);

        $hasHardware = false;
        $itemsToRequest = [];

        foreach ($request->items as $itemId => $qty) {
            $item = \App\Models\Item::findOrFail($itemId);
            if ($item->stock < $qty) {
                return back()->with('error', "Stok untuk barang {$item->name} tidak mencukupi.");
            }
            if ($item->category->name === 'Hardware') {
                $hasHardware = true;
            }
            $itemsToRequest[] = [
                'item' => $item,
                'quantity' => $qty,
            ];
        }

        // Tentukan return_date hanya jika ada kategori Hardware
        $returnDate = null;
        $note = "Tgl: {$request->tanggal} | Jam: {$request->jam_mulai} s/d {$request->jam_selesai}";
        
        if ($hasHardware) {
            if (!$request->return_date) {
                return back()->with('error', 'Tanggal pengembalian wajib diisi untuk peminjaman alat (Hardware).');
            }
            $returnDate = $request->return_date . ' ' . $request->jam_selesai;
        } else {
            $note .= ' | Sekali pakai, tidak perlu dikembalikan.';
        }

        // Peminjaman diwakili oleh 1 item_request karena struktur DB
        $itemRequest = \App\Models\ItemRequest::create([
            'user_id' => auth()->id(),
            'reporter_name' => auth()->user()->name,
            'reporter_email' => auth()->user()->email,
            'status' => 'pending',
            'request_date' => $request->tanggal . ' ' . $request->jam_mulai,
            'return_date' => $returnDate,
            'notes' => $note,
        ]);

        foreach ($itemsToRequest as $data) {
            \App\Models\ItemRequestDetail::create([
                'item_request_id' => $itemRequest->id,
                'item_id' => $data['item']->id,
                'quantity' => $data['quantity'],
            ]);
        }

        return redirect()->route('user.catalog.index')->with('success', 'Permintaan peminjaman berhasil terkirim!');
    }
}
