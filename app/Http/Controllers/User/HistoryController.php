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
            
            $isActiveA = (!$isBahanA && in_array($reqA->status, ['pending', 'approved', 'return-requested'])) ? 1 : 0;
            $isActiveB = (!$isBahanB && in_array($reqB->status, ['pending', 'approved', 'return-requested'])) ? 1 : 0;

            if ($isActiveA !== $isActiveB) {
                return $isActiveB - $isActiveA;
            }
            
            $timeA = $reqA->created_at ? $reqA->created_at->timestamp : 0;
            $timeB = $reqB->created_at ? $reqB->created_at->timestamp : 0;
            
            return $timeB - $timeA;
        });

        // Paginate collection
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        
        $paginatedDetails = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedDetails->forPage($page, $perPage)->values(),
            $sortedDetails->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('user.history.index', [
            'histories' => $paginatedDetails
        ]);
    }
}
