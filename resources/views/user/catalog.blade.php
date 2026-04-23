@extends('layouts.user')
@section('title', 'Permintaan Barang')

@section('content')
<style>
    .catalog-header h2 { font-size: 1.8rem; font-weight: 700; color: #000; margin-bottom: 5px; letter-spacing: -0.5px; }
    .catalog-header p { font-size: 0.95rem; color: #444; margin-bottom: 20px; }
    
    .filters { display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap; }
    .filter-btn { padding: 8px 24px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; cursor: pointer; border: none; transition: 0.3s; text-decoration:none; color: #333; background: #f0f0f0; display:inline-block; }
    .filter-btn.active { background-color: #c91a25; color: white; }
    
    /* Grid */
    .catalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; align-items: start; }
    
    /* Card */
    .item-card { border: 1px solid #777; border-radius: 16px; background: white; text-align: center; display: flex; flex-direction: column; position: relative; overflow:hidden; }
    .item-card:hover { border-color: #555; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    
    .img-box { background-color: #f6f6f6; height: 180px; display: flex; justify-content: center; align-items: center; position: relative; padding: 20px; border-bottom: 1px solid #eee; }
    .img-box img { max-width: 100%; max-height: 100%; object-fit: contain; mix-blend-mode: multiply; }
    
    .stock-badge { position: absolute; top: 15px; right: 15px; background-color: #a7ff7c; color: #222; font-size: 0.75rem; font-weight: 700; padding: 5px 12px; border-radius: 20px; z-index: 2; }
    
    .card-body { padding: 20px 15px; display:flex; flex-direction:column; flex:1; }
    .item-name { font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 5px; }
    .item-cat { font-size: 0.75rem; color: #666; margin-bottom: 20px; }
    
    .btn-pinjam { background-color: #e33e42; color: white; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; border: none; cursor: pointer; transition: 0.2s; width: 100%; margin-top: auto; box-shadow: 0 2px 4px rgba(227, 62, 66, 0.2); }
    .btn-pinjam:hover { background-color: #c91a25; }
    .btn-pinjam:disabled { background-color: #ccc; box-shadow: none; cursor: not-allowed; }

    /* Modals */
    .figma-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 1000; }
    .figma-modal { background: white; border-radius: 20px; width: 90%; max-width: 650px; overflow: hidden; padding: 0; position:relative; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
    
    .modal-hdr { padding: 15px 25px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between; }
    .modal-hdr h3 { font-size: 1.1rem; font-weight: 600; margin:0; }
    
    .modal-body-split { display: flex; padding: 25px; gap: 25px; }
    .left-side { flex: 0.8; display:flex; flex-direction:column; align-items:center; }
    .right-side { flex: 1.2; }
    
    .left-img-box { width: 100%; height: 200px; background: #e0e0e0; border-radius: 12px; display:flex; align-items:center; justify-content:center; padding: 15px; margin-bottom: 10px; }
    .left-img-box img { max-width: 100%; max-height: 100%; object-fit: contain; mix-blend-mode: multiply; }
    .stok-text-red { color: #e33e42; font-weight: 700; font-size: 0.8rem; margin-bottom: 15px; text-transform:uppercase; text-align:center; }
    .big-item-name { font-size: 1.3rem; font-weight: 700; text-align: center; text-transform:capitalize; }

    .fg-form { margin-bottom: 15px; text-align:left; }
    .fg-form label { display: block; font-size: 0.85rem; font-weight: 600; color: #333; margin-bottom: 5px; }
    .fg-input { width: 100%; padding: 10px 15px; border: 1px solid #ccc; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 0.9rem; }
    .note-text { font-size: 0.7rem; color: #888; font-style: italic; margin-top: 5px; }
    
    .modal-footer { display: flex; justify-content: center; gap: 15px; margin-top: 10px; }
    .btn-batal { background-color: #777; color: white; padding: 10px 30px; border-radius: 8px; border:none; font-weight: 600; cursor:pointer; }
    .btn-kirim { background-color: #e33e42; color: white; padding: 10px 25px; border-radius: 8px; border:none; font-weight: 600; cursor:pointer; }
</style>

<div class="catalog-header">
    <h2>Katalog Inventaris</h2>
    <p>Pilih alat yang akan anda pinjam hari ini</p>
</div>

<div class="filters">
    <a href="#" class="filter-btn active" data-filter="all">Semua Barang</a>
    @foreach($items->pluck('category.name')->unique() as $cat)
        <a href="#" class="filter-btn" data-filter="{{ strtolower(trim($cat)) }}">{{ $cat }}</a>
    @endforeach
</div>

@if(session('success'))
<div style="padding: 15px; background: rgba(39, 174, 96, 0.1); color: #27ae60; border: 1px solid rgba(39, 174, 96, 0.3); border-radius: 8px; margin-bottom: 20px;">
    <strong>Sukses!</strong> {{ session('success') }}
</div>
@endif

<div class="catalog-grid">
    @foreach($items as $item)
    <div class="item-card" data-category="{{ strtolower(trim($item->category->name)) }}">
        <div class="img-box">
            <div class="stock-badge">Tersedia: {{ $item->stock }} {{ $item->unit?->name }}</div>
            @if($item->photo)
                <img src="{{ asset($item->photo) }}" alt="{{ $item->name }}">
            @else
                <img src="https://via.placeholder.com/150?text=No+Img" alt="No Image">
            @endif
            @if($item->kode_barang)
                <div style="position: absolute; bottom: 10px; left: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-align: center;">
                    {{ $item->kode_barang }}
                </div>
            @endif
        </div>
        
        <div class="card-body">
            <h3 class="item-name">{{ $item->name }}</h3>
            <p class="item-cat">Lab Komputer | {{ $item->category->name }}</p>
            
            @if($item->stock > 0)
                <button class="btn-pinjam" onclick="openModal('requestModal{{ $item->id }}')">
                    Pinjam Sekarang
                </button>
            @else
                <button class="btn-pinjam" disabled style="background-color: #ccc; cursor: not-allowed;">
                    Stok Habis
                </button>
            @endif
        </div>
    </div>

    <!-- Modal Request Pinjam (Figma Match) -->
    <div class="figma-modal-overlay" id="requestModal{{$item->id}}">
        <div class="figma-modal">
            <div class="modal-hdr">
                <h3>Permintaan Barang</h3>
                <button type="button" onclick="closeModal('requestModal{{$item->id}}')" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666; padding: 0; margin: 0; line-height: 1;">&times;</button>
            </div>
            
            <form action="{{ route('user.requests.store') }}" method="POST">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <div class="modal-body-split">
                    <!-- Left Photo -->
                    <div class="left-side">
                        <div class="left-img-box">
                            @if($item->photo)
                                <img src="{{ asset($item->photo) }}" alt="{{ $item->name }}">
                            @else
                                <img src="https://via.placeholder.com/150?text=No+Img" alt="No Image">
                            @endif
                        </div>
                        <div class="stok-text-red">Stok tersedia: {{ $item->stock }} {{ $item->unit?->name }}</div>
                        <div class="big-item-name">{{ $item->name }}</div>
                    </div>
                    
                    <!-- Right Form -->
                    <div class="right-side">
                        <div class="fg-form">
                            <label>Tanggal permintaan</label>
                            <input type="date" name="tanggal" class="fg-input" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="fg-form">
                            <label>Jam mulai</label>
                            <input type="time" name="jam_mulai" class="fg-input" value="13:00" required>
                        </div>
                        <div class="fg-form">
                            <label>Jam selesai</label>
                            <input type="time" name="jam_selesai" class="fg-input" value="15:00" required>
                            <p class="note-text">Note: peminjaman hanya berlaku sehari jika ingin lebih, silahkan hubungi admin</p>
                        </div>
                        <div class="fg-form">
                            <label>Jumlah Barang</label>
                            <input type="number" name="quantity" class="fg-input" min="1" max="{{ $item->stock }}" value="1" required>
                        </div>
                        
                        @if($item->category->name === 'Hardware')
                        <div class="fg-form">
                            <label>Tanggal pengembalian</label>
                            <input type="date" name="return_date" class="fg-input" min="{{ date('Y-m-d') }}" required>
                            <p class="note-text">Wajib diisi untuk kategori Hardware</p>
                        </div>
                        @else
                        <div class="fg-form">
                            <div style="padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; margin-bottom: 10px;">
                                <strong>📦 Sekali pakai</strong><br>
                                <small>Barang ini hanya dipakai sesuai permintaan dan tidak perlu dikembalikan.</small>
                            </div>
                        </div>
                        @endif
                        
                        <div class="modal-footer">
                            <button type="button" class="btn-batal" onclick="closeModal('requestModal{{$item->id}}')">Batal</button>
                            <button type="submit" class="btn-kirim">Kirim Permintaan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
        }
    }
    
    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
        }
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('figma-modal-overlay')) {
            event.target.style.display = 'none';
        }
    });
    
    // Interaksi Filter Visual
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.dataset.filter;

            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.item-card').forEach(card => {
                if (!filter || filter === 'all') {
                    card.style.display = 'flex';
                } else {
                    const category = card.dataset.category?.toLowerCase() || '';
                    card.style.display = category === filter ? 'flex' : 'none';
                }
            });
        });
    });
</script>
@endsection
