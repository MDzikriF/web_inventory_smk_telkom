<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Message;

class SettingsController extends Controller
{
    public function index()
    {
        $autoClearChatEnabled = Setting::get('auto_clear_chat_enabled', false);
        $autoClearChatInterval = Setting::get('auto_clear_chat_interval', 'daily');
        
        return view('admin.settings.index', compact('autoClearChatEnabled', 'autoClearChatInterval'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'auto_clear_chat_enabled' => 'required|boolean',
            'auto_clear_chat_interval' => 'required|in:daily,weekly,monthly',
        ]);
        
        Setting::set('auto_clear_chat_enabled', $request->auto_clear_chat_enabled, 'boolean', 'Enable/disable auto clear chat feature');
        Setting::set('auto_clear_chat_interval', $request->auto_clear_chat_interval, 'string', 'Auto clear chat interval');
        
        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil diperbarui');
    }
    
    public function clearAllChat()
    {
        Message::truncate();
        
        return redirect()->route('admin.chat.index')->with('success', 'Semua chat berhasil dihapus');
    }
}
