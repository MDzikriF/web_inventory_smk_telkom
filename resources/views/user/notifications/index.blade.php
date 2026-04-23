@extends('layouts.user')
@section('title', 'Notifikasi')

@section('content')
<style>
    /* Tabs System */
    .nf-tabs { display: flex; gap: 20px; border-bottom: 2px solid #eee; margin-bottom: 25px; padding-bottom: 10px; }
    .nf-tab-btn { background: none; border: none; font-size: 1rem; font-weight: 700; color: #777; cursor: pointer; padding-bottom: 5px; position:relative; }
    .nf-tab-btn.active { color: #c91a25; }
    .nf-tab-btn.active::after { content:''; position:absolute; bottom: -12px; left:0; width:100%; height:3px; background-color:#c91a25; border-radius:3px; }

    .nf-tab-content { display: none; }
    .nf-tab-content.active { display: block; }

    /* Notifikasi List */
    .nf-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .nf-header h3 { font-size: 1.25rem; font-weight: 700; margin: 0; color: #000; }
    .nf-header a { font-size: 0.85rem; color: #c91a25; font-weight: 600; text-decoration: none; }

    .nf-list { display: flex; flex-direction: column; gap: 15px; }
    .nf-item { display: flex; align-items: flex-start; gap: 15px; background: white; padding: 15px; border-radius: 12px; border: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
    .nf-icon { width: 40px; height: 40px; background-color: #f0f0f0; border-radius: 8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .nf-body { flex: 1; }
    .nf-title { font-weight: 700; font-size: 0.95rem; color: #000; margin:0 0 5px; }
    .nf-desc { font-size: 0.85rem; color: #666; margin:0; line-height:1.4; }
    .nf-time { font-size: 0.75rem; color: #999; white-space: nowrap; }

    /* Widget Row */
    .nf-widgets { display: flex; gap: 20px; margin-top: 30px; }
    .nf-widget-card { flex: 1; background: white; border-radius: 12px; padding: 20px; border: 1px solid #ddd; display: flex; flex-direction: column; }
    .nf-wicon { width: 40px; height: 40px; border-radius: 10px; display:flex; align-items:center; justify-content:center; margin-bottom: 10px; font-weight: bold; }
    .nf-wh-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 5px; color:#000;}
    .nf-wh-desc { font-size: 0.8rem; color:#666; margin-bottom: 15px; line-height:1.4;}
    
    .nw-link-red { color: #c91a25; font-weight: 600; font-size: 0.85rem; text-decoration:none; margin-top:auto;}
    .nw-btn-red { background: #c91a25; color: white; border:none; padding:10px; border-radius:8px; font-weight:600; cursor:pointer;}

    /* Histori List */
    .hi-card { display: flex; align-items: center; gap: 15px; background: white; border: 1px solid #eee; border-radius: 16px; padding: 15px 25px; margin-bottom: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
    .hi-img { width: 80px; height: 80px; background: #f6f6f6; border-radius: 10px; display:flex; align-items:center; justify-content:center; overflow:hidden;}
    .hi-img img { max-width: 100%; max-height:100%; object-fit:contain; mix-blend-mode:multiply;}
    .hi-info { flex: 1; }
    .hi-info h4 { font-size: 1.2rem; font-weight: 700; margin:0 0 5px 0; color:#000;}
    .hi-info p { font-size: 0.85rem; font-weight: 600; color: #666; margin:0; }
    .hi-actions { text-align: center; }
    .hi-btn-return { background-color: #e33e42; color: white; padding: 8px 30px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; border: none; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 4px rgba(227, 62, 66, 0.2); margin-bottom: 5px; }
    .hi-btn-return:hover { background-color: #c91a25; }
    .hi-note { font-size: 0.7rem; color: #d63031; font-weight: 600; }

    /* Return Success Modal (Image 3 mockup) */
    .hm-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 1000; }
    .hm-modal { background: #fdfdfd; border-radius: 20px; width: 90%; max-width: 450px; padding: 40px 30px; text-align:center; position:relative; }
    .hm-check-icon { display:inline-flex; width: 80px; height: 80px; background-color: #a7ff7c; border-radius: 25px; align-items:center; justify-content:center; margin-bottom: 20px; transform: rotate(10deg); }
    .hm-check-icon svg { width: 45px; height: 45px; fill: white; transform: rotate(-10deg); }
    .hm-title { font-size: 1.3rem; font-weight: 700; color: #000; margin-bottom: 10px; line-height:1.2; }
    .hm-desc { font-size: 0.8rem; color: #666; margin-bottom: 20px; line-height:1.4; }
    
    .hm-details { background: #eee; border-radius: 12px; padding: 15px; text-align: left; margin-bottom: 25px; }
    .hm-row { display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color:#333; }
    
    .hm-btn-selesai { background-color: #e33e42; color: white; display:block; width:100%; padding: 12px; border-radius: 12px; font-weight: 700; font-size: 0.95rem; border: none; cursor: pointer; margin-bottom: 10px; }
    .hm-btn-pinjam { background-color: white; color: #e33e42; border: 2px solid #e33e42; display:block; width:100%; padding: 10px; border-radius: 12px; font-weight: 700; font-size: 0.95rem; cursor: pointer; text-decoration:none; }
</style>

@if(session('success') || session('error'))
    <div style="margin-bottom:20px; padding:15px; border-radius:12px; border: 1px solid #c91a25; background: {{ session('success') ? '#fef0f0' : '#fff0e5' }}; color: {{ session('success') ? '#9c1313' : '#a35500' }};">
        {{ session('success') ?? session('error') }}
    </div>
@endif

<form id="returnForm" action="" method="POST" style="display:none;">
    @csrf
</form>

<div class="nf-tabs">
    <button class="nf-tab-btn active" onclick="switchTab('notifikasi', this)">Pusat notifikasi</button>
    <button class="nf-tab-btn" onclick="switchTab('history', this)">Histori peminjaman</button>
</div>

<!-- TAB NOTIFIKASI -->
<div id="tab-notifikasi" class="nf-tab-content active">
    <div class="nf-header">
        <h3>Pemberitahuan Terbaru</h3>
        <a href="#">Tandai semua telah dibaca</a>
    </div>

    <div class="nf-list">
        @forelse($notifications as $notif)
            <div class="nf-item" style="opacity: {{ $notif->is_read ? '0.6' : '1' }}">
                <div class="nf-icon">
                    @if(str_contains(strtolower($notif->title), 'ditolak'))
                        🚫
                    @elseif(str_contains(strtolower($notif->title), 'pengingat'))
                        ⏰
                    @else
                        ✅
                    @endif
                </div>
                <div class="nf-body">
                    <h4 class="nf-title">{{ $notif->title }}</h4>
                    <p class="nf-desc">{{ $notif->message }}</p>
                </div>
                <div class="nf-time">{{ $notif->created_at->diffForHumans() }}</div>
            </div>
        @empty
            <div style="text-align:center; padding: 40px; color:#999; font-size:0.9rem;">
                Belum ada notifikasi.
            </div>
        @endforelse
    </div>

    <!-- Widgets -->
    <div class="nf-widgets">
        <div class="nf-widget-card" style="box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
            <div class="nf-wicon" style="border:1px solid #333; color:#333;">⚕</div>
            <h4 class="nf-wh-title">Bantuan lab</h4>
            <p class="nf-wh-desc">Hubungi admin lewat Chat untuk pertanyaan mendesak dan pelaporan</p>
            <a href="{{ route('user.chat.index') }}" class="nw-link-red">Buka Chat &rarr;</a>
        </div>
        
        <div class="nf-widget-card" style="background:#c91a25; color:white; box-shadow: 0 4px 15px rgba(201, 26, 37, 0.3);">
            <div class="nf-wicon" style="background:white; color:#c91a25;">+</div>
            <h4 class="nf-wh-title" style="color:white;">Pinjam lagi</h4>
            <p class="nf-wh-desc" style="color:rgba(255,255,255,0.8);">Pinjam lagi peralatan dan stok lab</p>
            <a href="{{ route('user.dashboard') }}" class="nw-btn-red" style="background:white; color:#c91a25; text-align:center;">Buat permintaan</a>
        </div>
    </div>
</div>

<!-- TAB HISTORY -->
<div id="tab-history" class="nf-tab-content">
    <div class="nf-header">
        <h3>Aktivitas peminjaman terbaru</h3>
    </div>

    @forelse($requests as $req)
        @foreach($req->details as $detail)
        <div class="hi-card">
            <div class="hi-img">
                @if($detail->item->photo)
                    <img src="{{ asset($detail->item->photo) }}" alt="Item">
                @else
                    <span style="color:#aaa;">Img</span>
                @endif
            </div>
            <div class="hi-info">
                <h4>{{ $detail->item->name ?? 'Aset Dihapus' }}</h4>
                <!-- Mengekstrak notes jika memungkinkan, atau tampilkan fallback waktu create -->
                <p>Dipinjam: {{ \Carbon\Carbon::parse($req->created_at)->format('d/m/Y (H:i)') }}</p>
            </div>
            
            <div class="hi-actions">
                @php
                    $isBahan = strtolower($detail->item->category->name ?? '') === 'bahan';
                @endphp
                
                @if($isBahan)
                    <span style="font-size:0.85rem; font-weight:600; color:#7f8c8d;">
                        📦 Selesai (Bahan)
                    </span>
                @else
                    @if($req->status == 'approved')
                        <button class="hi-btn-return" onclick="showReturnModal({{ json_encode($detail->item->name ?? 'Aset Dihapus') }}, {{ json_encode(\Carbon\Carbon::now()->format('H:i')) }}, {{ json_encode($req->id) }})">
                            Kembalikan
                        </button>
                        <!-- Mockup limit / due -->
                        <div class="hi-note">Batas penggunaan sisa 2 jam</div>
                    @elseif(in_array($req->status, ['return-requested', 'return_requested']))
                        <span style="font-size:0.85rem; font-weight:600; color:#2980b9;">
                            Menunggu konfirmasi admin
                        </span>
                    @else
                        <span style="font-size:0.85rem; font-weight:600; color:#27ae60;">
                            Selesai dikembalikan
                        </span>
                    @endif
                @endif
            </div>
        </div>
        @endforeach
    @empty
        <div style="text-align:center; padding: 40px; color:#999; font-size:0.9rem;">
            Belum ada aktivitas peminjaman.
        </div>
    @endforelse
</div>

<!-- RETURN MODAL OVERLAY -->
<div class="hm-overlay" id="returnModal">
    <div class="hm-modal">
        <div class="hm-check-icon">
            <svg viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
        </div>
        <h2 class="hm-title">Permintaan pengembalian terkirim</h2>
        <p class="hm-desc">Silahkan serahkan barang secara fisik ke petugas lab.<br>Status inventory akan diperbarui setelah diverifikasi admin.</p>
        
        <div class="hm-details">
            <div class="hm-row">
                <span>Barang</span>
                <span id="m_itemName">Stop kontak</span>
            </div>
            <div class="hm-row">
                <span>Jam pengembalian</span>
                <span id="m_itemTime">14:00</span>
            </div>
        </div>
        
        <button class="hm-btn-selesai" onclick="submitReturnRequest()">Selesai</button>
        <a href="{{ route('user.dashboard') }}" class="hm-btn-pinjam">Pinjam kembali</a>
    </div>
</div>

<script>
    const returnRequestBase = '{{ url('user/notifications') }}';

    function switchTab(tabId, btn) {
        document.querySelectorAll('.nf-tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.nf-tab-btn').forEach(el => el.classList.remove('active'));
        
        document.getElementById('tab-' + tabId).classList.add('active');
        btn.classList.add('active');
    }

    function showReturnModal(itemName, time, requestId) {
        document.getElementById('m_itemName').innerText = itemName;
        document.getElementById('m_itemTime').innerText = time;
        document.getElementById('returnForm').action = returnRequestBase + '/' + requestId + '/return-request';
        document.getElementById('returnModal').style.display = 'flex';
    }

    function submitReturnRequest() {
        document.getElementById('returnForm').submit();
    }

    function closeReturnModal() {
        document.getElementById('returnModal').style.display = 'none';
    }
</script>
@endsection
