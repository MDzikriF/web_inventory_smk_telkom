@extends('layouts.user')
@section('title', 'Dashboard')

@section('content')
<style>
    .welcome-card { background: linear-gradient(135deg, #c91a25, #e33e42); border-radius: 16px; padding: 30px; color: white; display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; box-shadow: 0 10px 20px rgba(227, 62, 66, 0.2); }
    .welcome-text h2 { font-size: 1.8rem; font-weight: 700; margin: 0 0 10px 0; }
    .welcome-text p { font-size: 0.95rem; opacity: 0.9; margin: 0; }
    
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: white; border: 1px solid #eee; border-radius: 16px; padding: 25px; display: flex; align-items: center; gap: 20px; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); border-color: #ddd; }
    .stat-icon { width: 60px; height: 60px; border-radius: 14px; background: rgba(201, 26, 37, 0.1); color: #c91a25; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; }
    
    .stat-info h3 { font-size: 2rem; font-weight: 800; margin: 0 0 5px 0; color: #000; }
    .stat-info p { font-size: 0.85rem; font-weight: 600; color: #777; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    .quick-actions { background: white; border: 1px solid #eee; border-radius: 16px; padding: 25px; }
    .qa-title { font-size: 1.25rem; font-weight: 700; margin: 0 0 20px 0; color: #000; }
    .qa-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
    .qa-btn { display: flex; align-items: center; gap: 15px; padding: 15px; background: #f6f6f6; border-radius: 12px; text-decoration: none; color: #333; font-weight: 600; transition: 0.2s; border: 1px solid transparent; }
    .qa-btn:hover { background: white; border-color: #c91a25; box-shadow: 0 4px 10px rgba(201, 26, 37, 0.1); color: #c91a25; }
    .qa-btn-icon { width: 40px; height: 40px; border-radius: 10px; background: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
</style>

<div class="welcome-card">
    <div class="welcome-text">
        <h2>Halo, {{ Auth::user()->name ?? 'User' }}!</h2>
        <p>Selamat datang di Pusat Kontrol Inventory Lab. Lihat ringkasan aktivitas peminjaman dan notifikasi Anda di sini.</p>
    </div>
    <div style="font-size: 4rem; opacity: 0.8;">👋</div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-info">
            <h3>{{ $totalPeminjaman }}</h3>
            <p>Total Peminjaman</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(39, 174, 96, 0.1); color: #27ae60;">⚡</div>
        <div class="stat-info">
            <h3>{{ $peminjamanAktif }}</h3>
            <p>Sedang Dipinjam</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;">⚠️</div>
        <div class="stat-info">
            <h3>{{ $totalPengaduan }}</h3>
            <p>Laporan Kerusakan</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(52, 152, 219, 0.1); color: #3498db;">🔔</div>
        <div class="stat-info">
            <h3>{{ $notifikasiBelumDibaca }}</h3>
            <p>Notifikasi Baru</p>
        </div>
    </div>
</div>

<div class="quick-actions">
    <h3 class="qa-title">Aksi Cepat</h3>
    <div class="qa-grid">
        <a href="{{ route('user.catalog.index') }}" class="qa-btn">
            <div class="qa-btn-icon">✚</div>
            Buat Peminjaman Baru
        </a>
        <a href="{{ route('user.complaints.index') }}" class="qa-btn">
            <div class="qa-btn-icon">🔧</div>
            Lapor Barang Rusak
        </a>
        <a href="{{ route('user.chat.index') }}" class="qa-btn">
            <div class="qa-btn-icon">💬</div>
            Hubungi Admin Lab
        </a>
    </div>
</div>
@endsection
