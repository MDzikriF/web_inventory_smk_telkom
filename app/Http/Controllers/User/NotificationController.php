<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = \App\Models\Notification::where('user_id', auth()->id())->latest()->get();
        
        // Mark all unread as read
        \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->update(['is_read' => true]);
        
        $requests = \App\Models\ItemRequest::with('details.item')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'return_requested', 'returned'])
            ->latest()
            ->get();
            
        return view('user.notifications.index', compact('notifications', 'requests'));
    }

    public function markAsRead($id)
    {
        $notification = \App\Models\Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['is_read' => true]);
        return back();
    }

    public function requestReturn($id)
    {
        $requestItem = \App\Models\ItemRequest::where('user_id', auth()->id())
            ->where('id', $id)
            ->where('status', 'approved')
            ->firstOrFail();

        $requestItem->update(['status' => 'return_requested']);

        \App\Models\Notification::create([
            'user_id' => auth()->id(),
            'title' => 'Permintaan pengembalian terkirim',
            'message' => 'Permintaan pengembalian untuk permintaan #' . str_pad($requestItem->id, 4, '0', STR_PAD_LEFT) . ' telah dikirim dan menunggu konfirmasi admin.',
            'is_read' => false,
        ]);

        $adminUsers = \App\Models\User::where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->nip,
                'title' => 'User meminta pengembalian',
                'message' => auth()->user()->name . ' mengajukan permintaan pengembalian untuk permintaan #' . str_pad($requestItem->id, 4, '0', STR_PAD_LEFT) . '.',
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Permintaan pengembalian berhasil dikirim. Silakan tunggu konfirmasi admin.');
    }
}
