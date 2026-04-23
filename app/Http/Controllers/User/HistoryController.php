<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $details = \App\Models\ItemRequestDetail::with(['itemRequest', 'item.category', 'item.unit'])
            ->whereHas('itemRequest', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->get();

        $sortedDetails = $details->sort(function ($a, $b) {
            $isBahanA = strtolower($a->item->category->name ?? '') === 'bahan';
            $isBahanB = strtolower($b->item->category->name ?? '') === 'bahan';
            
            $reqA = $a->itemRequest;
            $reqB = $b->itemRequest;
            
            // Prioritas Utama: ALAT yang BELUM selesai/dikembalikan (berstatus pending, approved, dll) ditaruh di paling ATAS
            // Bahan (yang tidak perlu dikembalikan) dan Alat (yang sudah dikembalikan) ditaruh di BAWAH.
            $isActiveA = (!$isBahanA && in_array($reqA->status, ['pending', 'approved', 'return-requested'])) ? 1 : 0;
            $isActiveB = (!$isBahanB && in_array($reqB->status, ['pending', 'approved', 'return-requested'])) ? 1 : 0;

            if ($isActiveA !== $isActiveB) {
                return $isActiveB - $isActiveA; // 1 before 0
            }
            
            // Prioritas Kedua: Urutan waktu (Terbaru ke Terlama)
            $timeA = $reqA->created_at ? $reqA->created_at->timestamp : 0;
            $timeB = $reqB->created_at ? $reqB->created_at->timestamp : 0;
            
            return $timeB - $timeA;
        });

        return view('user.history.index', [
            'histories' => $sortedDetails
        ]);
    }
}
