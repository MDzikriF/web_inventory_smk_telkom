<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ChatAdminController extends Controller
{
    public function index()
    {
        // Ambil admin utama
        $admin = User::where('role', 'admin')->first();
        
        return view('user.chat', compact('admin'));
    }

    public function getMessages(Request $request)
    {
        $adminNips = User::where('role', 'admin')->pluck('nip')->toArray();
        $messages = Message::where(function($query) use ($adminNips) {
            $query->where('sender_id', auth()->id())
                  ->whereIn('receiver_id', $adminNips);
        })->orWhere(function($query) use ($adminNips) {
            $query->whereIn('sender_id', $adminNips)
                  ->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();

        // Mark messages as read for user
        Message::where('receiver_id', auth()->user()->nip)
               ->where('is_read', false)
               ->update(['is_read' => true]);

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'nullable|string',
                'file' => 'nullable|file|max:10240', // 10MB
            ]);

            $admin = User::where('role', 'admin')->first();
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
                'receiver_id' => $admin ? $admin->nip : null, // Fallback if no admin exists
                'message' => $request->message,
                'file_path' => $filePath,
                'file_type' => $fileType,
            ]);

            // Kirim Notifikasi ke semua admin
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $ad) {
                \App\Models\Notification::create([
                    'user_id' => $ad->nip,
                    'title' => 'Pesan Baru dari User',
                    'message' => auth()->user()->name . ' mengirimkan pesan.',
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Pesan terkirim']);
        } catch (\Exception $e) {
            \Log::error('Chat send error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
