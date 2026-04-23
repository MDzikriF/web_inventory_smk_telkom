<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller {
    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255|unique:units,name']);
        Unit::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Satuan/Unit baru berhasil ditambahkan.');
    }
    public function destroy($id) {
        // Prevent deleting if connected to items
        $unit = Unit::findOrFail($id);
        if ($unit->items()->count() > 0) {
            return redirect()->back()->with('error', 'Satuan tidak bisa dihapus karena masih digunakan.');
        }
        $unit->delete();
        return redirect()->back()->with('success', 'Satuan berhasil dihapus.');
    }
}
