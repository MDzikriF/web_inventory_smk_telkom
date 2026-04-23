@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<div class="page-header" style="margin-bottom: 30px;">
    <h3 style="font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">Pengaturan Sistem</h3>
    <p style="color: var(--text-muted); margin: 0;">Kelola pengaturan fitur sistem</p>
</div>

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <h4 style="font-weight: 600; margin-bottom: 25px; color: var(--text-dark); border-bottom: 2px solid var(--primary); padding-bottom: 10px;">Pengaturan Chat</h4>
    
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 25px;">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 15px;">
                <div>
                    <h5 style="margin: 0 0 5px 0; font-weight: 600; color: var(--text-dark);">Auto Clear Chat</h5>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.9rem;">Aktifkan penghapusan otomatis chat sesuai interval yang ditentukan</p>
                </div>
                <label style="position: relative; display: inline-block; width: 60px; height: 34px;">
                    <input type="checkbox" name="auto_clear_chat_enabled" value="1" {{ $autoClearChatEnabled ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;" onchange="toggleAutoClear()">
                    <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;" id="autoClearToggle">
                        <span style="position: absolute; content: ''; height: 26px; width: 26px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;" id="autoClearToggleSlider"></span>
                    </span>
                </label>
                <input type="hidden" name="auto_clear_chat_enabled" value="0">
            </div>
            
            <div id="autoClearOptions" style="{{ $autoClearChatEnabled ? '' : 'display: none;' }} padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 20px;">
                <h5 style="margin: 0 0 15px 0; font-weight: 600; color: var(--text-dark);">Interval Auto Clear</h5>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                    <label style="display: flex; align-items: center; padding: 15px; background: white; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="auto_clear_chat_interval" value="daily" {{ $autoClearChatInterval == 'daily' ? 'checked' : '' }} style="margin-right: 10px;">
                        <div>
                            <div style="font-weight: 600;">Harian</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Setiap hari</div>
                        </div>
                    </label>
                    
                    <label style="display: flex; align-items: center; padding: 15px; background: white; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="auto_clear_chat_interval" value="weekly" {{ $autoClearChatInterval == 'weekly' ? 'checked' : '' }} style="margin-right: 10px;">
                        <div>
                            <div style="font-weight: 600;">Mingguan</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Setiap minggu</div>
                        </div>
                    </label>
                    
                    <label style="display: flex; align-items: center; padding: 15px; background: white; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="auto_clear_chat_interval" value="monthly" {{ $autoClearChatInterval == 'monthly' ? 'checked' : '' }} style="margin-right: 10px;">
                        <div>
                            <div style="font-weight: 600;">Bulanan</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Setiap bulan</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 15px;">
            <button type="submit" class="btn" style="background: var(--primary); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Simpan Pengaturan</button>
        </div>
    </form>
</div>

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 20px;">
    <h4 style="font-weight: 600; margin-bottom: 25px; color: var(--text-dark); border-bottom: 2px solid #e74c3c; padding-bottom: 10px;">Pengelolaan Chat</h4>
    
    <div style="padding: 20px; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 8px; margin-bottom: 20px;">
        <h5 style="margin: 0 0 10px 0; font-weight: 600; color: #c53030;">⚠️ Hapus Semua Chat</h5>
        <p style="margin: 0 0 15px 0; color: #742a2a; font-size: 0.9rem;">Tindakan ini akan menghapus SEMUA pesan chat dari sistem. Tindakan tidak dapat dibatalkan.</p>
        
        <form action="{{ route('admin.chat.clear_all') }}" method="POST" onsubmit="return confirmClearChat()" style="display: inline;">
            @csrf
            <button type="submit" class="btn" style="background: #e74c3c; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;">Hapus Semua Chat</button>
        </form>
    </div>
</div>

<style>
    input[type="checkbox"]:checked + span {
        background-color: var(--primary);
    }
    
    input[type="checkbox"]:checked + span span {
        transform: translateX(26px);
    }
    
    input[type="radio"]:checked + label {
        border-color: var(--primary);
        background: #f8f9fa;
    }
</style>

<script>
function toggleAutoClear() {
    const checkbox = document.querySelector('input[type="checkbox"][name="auto_clear_chat_enabled"]');
    const hiddenInput = document.querySelector('input[type="hidden"][name="auto_clear_chat_enabled"]');
    const options = document.getElementById('autoClearOptions');
    
    if (checkbox.checked) {
        hiddenInput.value = '1';
        options.style.display = 'block';
    } else {
        hiddenInput.value = '0';
        options.style.display = 'none';
    }
}

function confirmClearChat() {
    return confirm('Apakah Anda yakin ingin menghapus SEMUA chat? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua pesan dari sistem.');
}

// Initialize toggle state
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.querySelector('input[type="checkbox"][name="auto_clear_chat_enabled"]');
    const toggle = document.getElementById('autoClearToggle');
    const slider = document.getElementById('autoClearToggleSlider');
    
    if (checkbox.checked) {
        toggle.style.backgroundColor = 'var(--primary)';
        slider.style.transform = 'translateX(26px)';
    }
});
</script>
@endsection
