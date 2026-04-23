<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;

class ItemController extends Controller
{
    public function index()
    {
        \Log::info('=== INDEX METHOD CALLED ===');
        
        $items = Item::with(['category', 'unit'])->latest()->get();
        $categories = Category::all();
        $units = Unit::all();
        $lowStockItems = Item::whereColumn('stock', '<', 'min_stock')->get();
        
        \Log::info('Items count:', ['count' => $items->count()]);
        \Log::info('Items list:', $items->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'kode_barang' => $item->kode_barang,
                'created_at' => $item->created_at
            ];
        })->toArray());
        
        // Create notifications for low stock
        if ($lowStockItems->isNotEmpty() && auth()->check()) {
            $admin = auth()->user();
            foreach ($lowStockItems as $item) {
                $existing = \App\Models\Notification::where('user_id', $admin->nip)
                    ->where('title', 'Stok Minimum')
                    ->where('message', 'like', '%' . $item->name . '%')
                    ->where('created_at', '>', now()->subDay())
                    ->first();
                
                if (!$existing) {
                    \App\Models\Notification::create([
                        'user_id' => $admin->nip,
                        'title' => 'Stok Minimum',
                        'message' => "Item {$item->name} memiliki stok {$item->stock} yang di bawah minimum {$item->min_stock}.",
                    ]);
                }
            }
        }
        
        \Log::info('Returning view with items');
        return view('admin.items.index', compact('items', 'categories', 'units', 'lowStockItems'));
    }

    public function store(Request $request)
    {
        \Log::info('=== STORE METHOD CALLED ===');
        \Log::info('Request data:', $request->all());
        
        $request->validate([
            'name' => 'required|string|max:255',
            'kode_barang' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'sub_kategori' => 'nullable|in:KBM,Khusus',
            'stock' => 'required|integer|min:0',
            'photo' => 'nullable|image|max:2048'
        ]);

        \Log::info('Validation passed');

        $data = $request->all();
        \Log::info('Data before create:', $data);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('items', 'public');
            $data['photo'] = 'storage/' . $path;
            \Log::info('Photo uploaded:', ['path' => $data['photo']]);
        }

        $item = Item::create($data);
        \Log::info('Item created:', ['id' => $item->id, 'name' => $item->name, 'kode_barang' => $item->kode_barang]);

        // Rekam riwayat transaksi masuk
        if ($item->stock > 0) {
            \App\Models\Transaction::create([
                'item_id' => $item->id,
                'type' => 'in',
                'quantity' => $item->stock,
                'date' => now()->toDateString(),
                'notes' => 'Pasokan awal barang baru'
            ]);
            \Log::info('Transaction created');
        }

        // Handle AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil ditambahkan.',
                'item' => $item
            ]);
        }

        \Log::info('Redirecting back with success message');
        return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'kode_barang' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'sub_kategori' => 'nullable|in:KBM,Khusus',
            'stock' => 'required|integer|min:0',
            'photo' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('items', 'public');
            $data['photo'] = 'storage/' . $path;
        }

        $oldStock = $item->stock;
        $item->update($data);

        // Rekam riwayat mutasi stok jika ada selisih angka
        if ($item->stock > $oldStock) {
            \App\Models\Transaction::create([
                'item_id' => $item->id,
                'type' => 'in',
                'quantity' => $item->stock - $oldStock,
                'date' => now()->toDateString(),
                'notes' => 'Penambahan / restock barang'
            ]);
        } elseif ($item->stock < $oldStock) {
            \App\Models\Transaction::create([
                'item_id' => $item->id,
                'type' => 'out',
                'quantity' => $oldStock - $item->stock,
                'date' => now()->toDateString(),
                'notes' => 'Koreksi cacat / pengurangan stok oleh Admin'
            ]);
        }

        return redirect()->back()->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }
}
