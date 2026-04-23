<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'return_date' => 'nullable|date|after_or_equal:tanggal',
        ]);

        $item = \App\Models\Item::findOrFail($request->item_id);
        if ($item->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        // Tentukan return_date hanya untuk kategori Hardware
        $returnDate = null;
        $note = "Tgl: {$request->tanggal} | Jam: {$request->jam_mulai} s/d {$request->jam_selesai}";
        if ($item->category->name === 'Hardware') {
            $returnDate = $request->return_date ? $request->tanggal . ' ' . $request->jam_selesai : null;
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

        \App\Models\ItemRequestDetail::create([
            'item_request_id' => $itemRequest->id,
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('user.catalog.index')->with('success', 'Permintaan peminjaman berhasil terkirim!');
    }
}
