@extends('layouts.user')
@section('title', 'Pengaduan Alat Rusak')

@section('content')
<h1 class="page-title">Pusat Pelaporan Kerusakan</h1>

<style>
    .complaint-card {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 15px;
        border: 1px solid #eee;
        border-radius: 12px;
        margin-bottom: 15px;
        background: #fafafa;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .complaint-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }
    .c-date {
        flex-shrink: 0;
        text-align: center;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px;
        min-width: 65px;
    }
    .c-date strong { display: block; font-size: 1.3rem; color: var(--text-dark); line-height: 1; margin-bottom: 3px; font-weight: 800; }
    .c-date small { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; }
    
    .c-info { flex: 1; }
    .c-info h4 { margin: 0 0 5px 0; font-size: 1.05rem; font-weight: 700; color: var(--text-dark); }
    .c-info p { margin: 0 0 10px 0; font-size: 0.85rem; color: #555; line-height: 1.4; word-break: break-word;}
    .c-photo-link { display: inline-flex; align-items: center; gap: 5px; font-size: 0.8rem; color: var(--primary); text-decoration: none; font-weight: 600; background: rgba(220, 53, 69, 0.08); padding: 5px 12px; border-radius: 20px; transition: 0.2s; }
    .c-photo-link:hover { background: rgba(220, 53, 69, 0.15); }
    
    .c-status { text-align: right; flex-shrink: 0; }
    .status-badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;}
    .status-pending { background: rgba(241, 196, 15, 0.15); color: #d35400; }
    .status-reviewed { background: rgba(52, 152, 219, 0.15); color: #2980b9; }
    .status-resolved { background: rgba(39, 174, 96, 0.15); color: #27ae60; }

    @media (max-width: 600px) {
        .complaint-card { flex-wrap: wrap; }
        .c-status { flex-basis: 100%; text-align: left; margin-top: 10px; }
    }
</style>

<div style="display:flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
    <!-- Form Lapor -->
    <div class="card" style="flex: 1; min-width: 300px;">
        <h3 style="font-weight: 600; color: var(--text-dark); margin-bottom: 20px;">Buat Laporan Baru</h3>
        
        @if(session('success'))
            <div style="padding: 15px; background: rgba(39, 174, 96, 0.1); color: #27ae60; border-radius: 8px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('user.complaints.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Pilih Aset Bermasalah</label>
                <select name="item_id" class="form-control" required style="background:white;">
                    <option value="">-- Silakan Pilih --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->category->name }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Kenapa dilaporkan? (Kronologi/Kerusakan)</label>
                <textarea name="notes" class="form-control" rows="4" required placeholder="Jelaskan kendala atau kerusakan yang dialami dengan detail..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Bukti Foto Kerusakan (Wajib)</label>
                <input type="file" name="photo" class="form-control" accept="image/*" required>
                <small style="color:var(--text-muted); display:block; margin-top:5px;">Maksimal ukuran file 2MB.</small>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Kirim Laporan</button>
        </form>
    </div>

    <!-- Riwayat Laporan -->
    <div class="card" style="flex: 1.5; min-width: 350px;">
        <h3 style="font-weight: 600; color: var(--text-dark); margin-bottom: 20px;">Riwayat Pengaduan Anda</h3>
        
        @if($complaints->isEmpty())
            <div style="text-align:center; padding: 40px 20px; color: var(--text-muted);">
                <span style="font-size: 3rem; display:block; margin-bottom:10px;">🛡️</span>
                Anda belum pernah mengajukan laporan kerusakan.
            </div>
        @else
            <div class="complaint-list">
                @foreach($complaints as $c)
                    <div class="complaint-card">
                        <div class="c-date">
                            <strong>{{ $c->created_at->format('d') }}</strong>
                            <small>{{ $c->created_at->format('M Y') }}</small>
                        </div>
                        <div class="c-info">
                            <h4>{{ $c->item->name ?? 'Aset Dihapus' }}</h4>
                            <p>{{ Str::limit($c->notes, 80) }}</p>
                            @if($c->photo)
                                <a href="{{ asset($c->photo) }}" target="_blank" class="c-photo-link">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                    Lihat Bukti Foto
                                </a>
                            @endif
                        </div>
                        <div class="c-status">
                            @if($c->status == 'pending')
                                <span class="status-badge status-pending">MENUNGGU TINJAUAN</span>
                            @elseif($c->status == 'reviewed')
                                <span class="status-badge status-reviewed">SEDANG DIPERIKSA</span>
                            @else
                                <span class="status-badge status-resolved">SELESAI TERATASI</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
