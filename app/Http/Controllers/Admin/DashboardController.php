<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;

class DashboardController extends Controller
{
    public function index()
    {
        $lowStockItems = Item::whereColumn('stock', '<', 'min_stock')->get();
        
        // Create notifications for low stock
        if ($lowStockItems->isNotEmpty()) {
            $admin = auth()->user(); // Assuming admin is logged in
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
        
        return view('admin.dashboard', compact('lowStockItems'));
    }
}