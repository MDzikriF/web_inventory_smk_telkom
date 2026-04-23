<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Menyajikan ringkasan statistik ke menu Dashboard
        $userId = auth()->id();
        
        $totalPeminjaman = \App\Models\ItemRequest::where('user_id', $userId)->count();
        $peminjamanAktif = \App\Models\ItemRequest::where('user_id', $userId)->where('status', 'approved')->count();
        
        $totalPengaduan = \App\Models\DamageReport::where('user_id', $userId)->count();
        
        $notifikasiBelumDibaca = \App\Models\Notification::where('user_id', $userId)->where('is_read', false)->count();

        return view('user.overview', compact(
            'totalPeminjaman', 
            'peminjamanAktif', 
            'totalPengaduan', 
            'notifikasiBelumDibaca'
        ));
    }
}
