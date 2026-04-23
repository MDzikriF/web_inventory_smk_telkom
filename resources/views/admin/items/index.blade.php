@extends('layouts.admin')
@section('title', 'Kelola Barang')

@section('content')
<h1 class="page-title">Kelola Barang</h1>

@if(session('error'))
<div style="padding: 15px; background: rgba(231, 76, 60, 0.1); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.3); border-radius: 8px; margin-bottom: 20px;">
    <strong>Error!</strong> {{ session('error') }}
</div>
@endif

@if($lowStockItems->count() > 0)
<div class="alert alert-warning" style="margin-bottom: 20px; border-left: 4px solid #f39c12;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <div>
            <h4 style="margin: 0 0 5px 0; color: #e67e22;">⚠️ Peringatan Stok Rendah</h4>
            <p style="margin: 0; color: #7f8c8d;">Barang berikut memiliki stok di bawah minimum:</p>
            <ul style="margin: 5px 0 0 0; padding-left: 20px;">
                @foreach($lowStockItems as $lowItem)
                <li style="color: #34495e;"><strong>{{ $lowItem->name }}</strong> - Stok: {{ $lowItem->stock }}, Minimum: {{ $lowItem->min_stock }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-weight: 600; color: var(--text-dark);">Daftar Barang Inventaris</h3>
        <div style="display:flex; gap:10px;">
            <button class="btn" style="background:#f1f2f6; color:var(--text-dark); border:none; display:flex; align-items:center; gap:8px;" onclick="openModal('categoryModal')">
                ⚙️ Kategori & Satuan
            </button>
            <button class="btn btn-primary" onclick="openModal('addModal')" style="display:flex; align-items:center; gap:8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Barang
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama Barang</th>
                    <th>Kode Barang</th>
                    <th>Kategori</th>
                    <th>Stok Tersedia</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr style="{{ $item->stock < 5 ? 'background-color: rgba(243, 156, 18, 0.1); border-left: 3px solid #f39c12;' : '' }}">
                    <td>
                        @if($item->photo)
                            <img src="{{ asset($item->photo) }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; display: block;" alt="{{ $item->name }}">
                        @else
                            <img src="https://via.placeholder.com/50x50.png?text=Item" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; display: block;" alt="No Image">
                        @endif
                        @if($item->stock < 5)
                            <span style="display: block; font-size: 0.7rem; color: #e67e22; margin-top: 2px;">⚠️ Stok Rendah</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $item->name }}</strong>
                        @if($item->type)
                            <br><small style="color: #666;">Type: {{ $item->type }}</small>
                        @endif
                        @if($item->sub_kategori)
                            <br><small style="color: {{ $item->sub_kategori == 'KBM' ? '#27ae60' : '#e74c3c' }};">{{ $item->sub_kategori }}</small>
                        @endif
                    </td>
                    <td>
                        <span style="font-family: monospace; font-weight: 600; color: #2c3e50; background: #f8f9fa; padding: 2px 6px; border-radius: 4px; border: 1px solid #dee2e6;">
                            {{ $item->kode_barang ?? '-' }}
                        </span>
                    </td>
                    <td style="color: var(--text-muted);">
                        {{ $item->category->name }}
                        @if($item->sub_kategori)
                            <br><small>{{ $item->sub_kategori }}</small>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight: 700; font-size: 1.1rem; color: {{ $item->stock > 0 ? ($item->stock < 5 ? '#e67e22' : '#27ae60') : '#e74c3c' }}">
                            {{ $item->stock }}
                        </span> {{ $item->unit->name }}
                        @if($item->stock < 5)
                            <span style="font-size: 0.8rem; color: #e67e22;"> (Minimum: {{ $item->min_stock }})</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; gap:10px; justify-content:center;">
                            <button class="action-btn" title="Edit" onclick="openModal('editModal{{$item->id}}')">✏️</button>
                            <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus barang ini permanen?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn" title="Hapus">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>

                @push('modals')
                <!-- Edit Item Modal -->
                <div class="modal-overlay" id="editModal{{$item->id}}">
                    <div class="modal">
                        <div class="modal-header">
                            <h3 style="font-weight: 700;">Edit Barang</h3>
                            <button class="close-modal" type="button" onclick="closeModal('editModal{{$item->id}}')">&times;</button>
                        </div>
                        <form action="{{ route('admin.items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label class="form-label">Nama Barang</label>
                                <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kode Barang</label>
                                <input type="text" name="kode_barang" class="form-control" value="{{ $item->kode_barang ?? '' }}" placeholder="Cth: PC-001">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Type</label>
                                <input type="text" name="type" class="form-control" value="{{ $item->type ?? '' }}" placeholder="Cth: Core i5, DDR4, dll">
                            </div>
                            <div style="display:flex; gap:15px;">
                                <div class="form-group" style="flex:1;">
                                    <label class="form-label">Kategori</label>
                                    <select name="category_id" class="form-control" required style="background:white;">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" style="width: 120px;">
                                    <label class="form-label">Satuan</label>
                                    <select name="unit_id" class="form-control" required style="background:white;">
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ $item->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sub Kategori</label>
                                <div style="display:flex; gap:20px; margin-top:10px;">
                                    <label style="display:flex; align-items:center; cursor:pointer;">
                                        <input type="radio" name="sub_kategori" value="KBM" {{ $item->sub_kategori == 'KBM' ? 'checked' : '' }} style="margin-right:8px;">
                                        <span>KBM (Kegiatan Belajar Mengajar)</span>
                                    </label>
                                    <label style="display:flex; align-items:center; cursor:pointer;">
                                        <input type="radio" name="sub_kategori" value="Khusus" {{ $item->sub_kategori == 'Khusus' ? 'checked' : '' }} style="margin-right:8px;">
                                        <span>Khusus (Lab/Tester)</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jumlah Stok</label>
                                <input type="number" min="0" name="stock" class="form-control" value="{{ $item->stock }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Foto Item (Opsional)</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
                @endpush

                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('modals')
<!-- Add Item Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div class="modal-header">
            <h3 style="font-weight: 700;">Tambah Barang Baru</h3>
            <button class="close-modal" type="button" onclick="closeModal('addModal')">&times;</button>
        </div>
        <form id="addItemForm" action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Barang</label>
                <input type="text" name="name" class="form-control" required placeholder="Cth: PC All-In-One...">
            </div>
            <div class="form-group">
                <label class="form-label">Kode Barang</label>
                <input type="text" name="kode_barang" class="form-control" placeholder="Cth: PC-001">
            </div>
            <div class="form-group">
                <label class="form-label">Type</label>
                <input type="text" name="type" class="form-control" placeholder="Cth: Core i5, DDR4, dll">
            </div>
            <div style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-control" required style="background:white;">
                        <option value="">-- Pilih --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="width: 120px;">
                    <label class="form-label">Satuan</label>
                    <select name="unit_id" class="form-control" required style="background:white;">
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Sub Kategori</label>
                <div style="display:flex; gap:20px; margin-top:10px;">
                    <label style="display:flex; align-items:center; cursor:pointer;">
                        <input type="radio" name="sub_kategori" value="KBM" style="margin-right:8px;">
                        <span>KBM (Kegiatan Belajar Mengajar)</span>
                    </label>
                    <label style="display:flex; align-items:center; cursor:pointer;">
                        <input type="radio" name="sub_kategori" value="Khusus" style="margin-right:8px;">
                        <span>Khusus (Lab/Tester)</span>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah Stok</label>
                <input type="number" min="0" name="stock" class="form-control" required placeholder="0">
            </div>
            <div class="form-group">
                <label class="form-label">Foto Item (Opsional)</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Tambahkan ke Inventaris</button>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.getElementById('addItemForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.textContent = 'Menambahkan...';
    submitBtn.disabled = true;
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('addModal');
            location.reload();
        } else {
            alert('Terjadi kesalahan: ' + (data.message || 'Silakan coba lagi'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
        this.submit();
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});
</script>
@endpush

@push('modals')
<!-- Manage Categories & Units Modal -->
<div class="modal-overlay" id="categoryModal">
    <div class="modal" style="max-width: 600px;">
        <div class="modal-header">
            <h3 style="font-weight: 700;">Pengaturan Data Induk</h3>
            <button class="close-modal" type="button" onclick="closeModal('categoryModal')">&times;</button>
        </div>
        
        <div style="display:flex; gap:20px;">
            <!-- Categories Column -->
            <div style="flex:1;">
                <h4 style="margin-bottom:15px; color:var(--primary);">Daftar Kategori</h4>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <p style="margin: 0; font-size: 0.9rem; color: #666;">
                        <strong>Kategori tetap:</strong><br>
                        • <strong>Alat</strong> - Barang yang dipinjam dengan durasi (ada tanggal kembali)<br>
                        • <strong>Bahan</strong> - Barang habis pakai (tidak dikembalikan)
                    </p>
                </div>
                <div style="max-height: 200px; overflow-y:auto; border:1px solid var(--border-color); border-radius:8px;">
                    <table style="margin:0; font-size:0.9rem;">
                        @foreach($categories as $cat)
                        <tr>
                            <td style="padding:8px 12px;">{{ $cat->name }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <!-- Units Column -->
            <div style="flex:1;">
                <h4 style="margin-bottom:15px; color:var(--primary);">Daftar Satuan (Unit)</h4>
                <form action="{{ route('admin.units.store') }}" method="POST" style="display:flex; gap:10px; margin-bottom:15px;">
                    @csrf
                    <input type="text" name="name" class="form-control" placeholder="Satuan baru..." required style="padding:8px; font-size:0.9rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 8px 12px; font-size:0.9rem;">+</button>
                </form>
                <div style="max-height: 200px; overflow-y:auto; border:1px solid var(--border-color); border-radius:8px;">
                    <table style="margin:0; font-size:0.9rem;">
                        @foreach($units as $unit)
                        <tr>
                            <td style="padding:8px 12px;">{{ $unit->name }}</td>
                            <td style="width:40px; text-align:center;">
                                <form action="{{ route('admin.units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('Hapus Satuan?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="color:#e74c3c; background:none; border:none; cursor:pointer;">✖</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush
@endsection
