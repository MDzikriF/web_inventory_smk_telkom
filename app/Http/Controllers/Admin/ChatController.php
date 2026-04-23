<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('role', 'user')->get();
        $selectedUser = null;
        $messages = collect();

        $adminNips = User::where('role', 'admin')->pluck('nip')->toArray();

        if ($request->has('user_id')) {
            $selectedUser = User::findOrFail($request->user_id);
            $messages = Message::where(function($query) use ($selectedUser, $adminNips) {
                $query->whereIn('sender_id', $adminNips)
                      ->where('receiver_id', $selectedUser->nip);
            })->orWhere(function($query) use ($selectedUser, $adminNips) {
                $query->where('sender_id', $selectedUser->nip)
                      ->whereIn('receiver_id', $adminNips);
            })->orderBy('created_at', 'asc')->get();
        }

        return view('admin.chat', compact('users', 'selectedUser', 'messages', 'adminNips'));
    }

    public function getMessages(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $adminNips = User::where('role', 'admin')->pluck('nip')->toArray();
        $messages = Message::where(function($query) use ($user, $adminNips) {
            $query->whereIn('sender_id', $adminNips)
                  ->where('receiver_id', $user->nip);
        })->orWhere(function($query) use ($user, $adminNips) {
            $query->where('sender_id', $user->nip)
                  ->whereIn('receiver_id', $adminNips);
        })->orderBy('created_at', 'asc')->get();

        // Mark messages as read for admin
        Message::where('receiver_id', auth()->user()->nip)
               ->where('sender_id', $user->nip)
               ->where('is_read', false)
               ->update(['is_read' => true]);

        return response()->json($messages);
    }

    public function sendMessage(Request $request, $userId)
    {
        try {
            $request->validate([
                'message' => 'nullable|string',
                'file' => 'nullable|file|max:10240',
            ]);

            $user = User::findOrFail($userId);
            $filePath = null;
            $fileType = 'text';

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $mime = $file->getMimeType();
                if (str_contains($mime, 'image')) {
                    $fileType = 'image';
                } elseif (str_contains($mime, 'video')) {
                    $fileType = 'video';
                }
                $filePath = 'storage/' . $file->store('chat_files', 'public');
            }

            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $user->nip,
                'message' => $request->message,
                'file_path' => $filePath,
                'file_type' => $fileType,
            ]);

            \App\Models\Notification::create([
                'user_id' => $user->nip,
                'title' => 'Balasan dari Admin',
                'message' => 'Admin telah membalas pesan chat Anda.',
            ]);

            return response()->json(['success' => true, 'message' => 'Pesan terkirim']);
        } catch (\Exception $e) {
            \Log::error('Admin chat send error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
