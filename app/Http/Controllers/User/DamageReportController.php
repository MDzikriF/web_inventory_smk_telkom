<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DamageReportController extends Controller
{
    public function index()
    {
        $complaints = \App\Models\DamageReport::with('item')->where('user_id', auth()->id())->latest()->get();
        // Cukup kirimkan daftar item untuk dropdown milih barang yg rusak
        $items = \App\Models\Item::orderBy('name')->get(); 
        return view('user.complaints.index', compact('complaints', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'notes' => 'required|string',
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $path = $request->file('photo')->store('complaints', 'public');

        \App\Models\DamageReport::create([
            'user_id' => auth()->id(),
            'reporter_name' => auth()->user()->name,
            'reporter_email' => auth()->user()->email,
            'item_id' => $request->item_id,
            'notes' => $request->notes,
            'photo' => 'storage/' . $path,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Laporan kerusakan berhasil dikirim, tim kami akan segera meninjaunya.');
    }
}
