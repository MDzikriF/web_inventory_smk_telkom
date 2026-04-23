<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $histories = \App\Models\ItemRequest::with(['user', 'details.item'])
            ->whereIn('status', ['approved', 'return_requested', 'returned', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $transactions = \App\Models\Transaction::with('item')->orderBy('created_at', 'desc')->get();
        $damageReports = \App\Models\DamageReport::with(['item', 'user'])->orderBy('created_at', 'desc')->get();

        return view('admin.notifications.index', compact('notifications', 'histories', 'transactions', 'damageReports'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
