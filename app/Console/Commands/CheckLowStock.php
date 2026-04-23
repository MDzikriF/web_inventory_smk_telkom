<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:check-low-stock')]
#[Description('Command description')]
class CheckLowStock extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lowStockItems = \App\Models\Item::whereColumn('stock', '<', 'min_stock')->get();
        
        if ($lowStockItems->isNotEmpty()) {
            $admins = \App\Models\User::where('role', 'admin')->get();
            
            foreach ($admins as $admin) {
                foreach ($lowStockItems as $item) {
                    // Check if notification already exists
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
            
            $this->info('Notifikasi stok minimum telah dibuat.');
        } else {
            $this->info('Tidak ada item dengan stok rendah.');
        }
    }
}
