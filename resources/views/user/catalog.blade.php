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
    .catalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; align-items: start; padding-bottom: 100px; /* Space for floating bar */ }
    
    /* Card */
    .item-card { border: 2px solid transparent; border-radius: 16px; background: white; text-align: center; display: flex; flex-direction: column; position: relative; overflow:hidden; transition: 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.05); cursor: pointer; }
    .item-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .item-card.selected { border-color: #e33e42; background-color: #fff9f9; }
    .item-card.disabled { opacity: 0.5; cursor: not-allowed; }
    .item-card.disabled:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    
    .img-box { background-color: #f6f6f6; height: 180px; display: flex; justify-content: center; align-items: center; position: relative; padding: 20px; border-bottom: 1px solid #eee; transition: 0.3s; }
    .item-card.selected .img-box { background-color: #ffeaea; }
    .img-box img { max-width: 100%; max-height: 100%; object-fit: contain; mix-blend-mode: multiply; }
    
    .stock-badge { position: absolute; top: 15px; right: 15px; background-color: #a7ff7c; color: #222; font-size: 0.75rem; font-weight: 700; padding: 5px 12px; border-radius: 20px; z-index: 2; }
    
    /* Custom Checkbox */
    .custom-checkbox-wrapper { position: absolute; top: 15px; left: 15px; z-index: 2; }
    .custom-checkbox-wrapper input { display: none; }
    .checkmark { width: 24px; height: 24px; background-color: #fff; border: 2px solid #ccc; border-radius: 6px; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
    .custom-checkbox-wrapper input:checked + .checkmark { background-color: #e33e42; border-color: #e33e42; }
    .custom-checkbox-wrapper input:checked + .checkmark::after { content: '\2714'; color: white; font-size: 14px; font-weight: bold; }
    .custom-checkbox-wrapper input:disabled + .checkmark { background-color: #eee; border-color: #ddd; }
    
    .card-body { padding: 20px 15px; display:flex; flex-direction:column; flex:1; }
    .item-name { font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 5px; }
    .item-cat { font-size: 0.75rem; color: #666; margin-bottom: 0; }
    
    /* Floating Checkout Bar */
    .floating-checkout { position: fixed; bottom: -100px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 600px; background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); padding: 15px 25px; display: flex; align-items: center; justify-content: space-between; transition: bottom 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 999; border: 1px solid #eee; }
    .floating-checkout.visible { bottom: 30px; }
    .checkout-info { display: flex; flex-direction: column; }
    .checkout-count { font-size: 1.1rem; font-weight: 700; color: #333; }
    .checkout-cat { font-size: 0.8rem; color: #888; }
    .btn-checkout { background-color: #e33e42; color: white; padding: 10px 25px; border-radius: 10px; font-weight: 600; font-size: 1rem; border: none; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 10px rgba(227, 62, 66, 0.3); }
    .btn-checkout:hover { background-color: #c91a25; transform: translateY(-2px); }
    .btn-batal-katalog { background-color: #f8f9fa; color: #333; padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 1rem; border: 1px solid #ddd; cursor: pointer; transition: 0.2s; }
    .btn-batal-katalog:hover { background-color: #e2e6ea; transform: translateY(-2px); }

    /* Modals */
    .figma-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 1000; }
    .figma-modal { background: white; border-radius: 20px; width: 90%; max-width: 500px; overflow: hidden; padding: 0; position:relative; box-shadow: 0 10px 40px rgba(0,0,0,0.2); max-height: 90vh; display: flex; flex-direction: column; }
    .figma-modal form { display: flex; flex-direction: column; flex: 1; min-height: 0; margin: 0; }
    
    .modal-hdr { padding: 15px 25px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
    .modal-hdr h3 { font-size: 1.1rem; font-weight: 600; margin:0; }
    
    .modal-body { padding: 25px; overflow-y: auto; flex: 1; }
    
    /* Selected Items List in Modal */
    .selected-items-list { margin-bottom: 20px; border: 1px solid #eee; border-radius: 10px; padding: 10px; max-height: 200px; overflow-y: auto; }
    .selected-item-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #eee; }
    .selected-item-row:last-child { border-bottom: none; }
    .s-item-name { font-weight: 600; font-size: 0.9rem; flex: 1; }
    .s-item-qty { width: 70px; }
    .s-item-qty input { width: 100%; padding: 5px 10px; border: 1px solid #ccc; border-radius: 6px; text-align: center; }

    .fg-form { margin-bottom: 15px; text-align:left; }
    .fg-form label { display: block; font-size: 0.85rem; font-weight: 600; color: #333; margin-bottom: 5px; }
    .fg-input { width: 100%; padding: 10px 15px; border: 1px solid #ccc; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 0.9rem; }
    .note-text { font-size: 0.7rem; color: #888; font-style: italic; margin-top: 5px; }
    
    .modal-footer { display: flex; justify-content: center; gap: 15px; padding: 15px 25px; border-top: 1px solid #eee; background: #fdfdfd; flex-shrink: 0; }
    .btn-batal { background-color: #777; color: white; padding: 10px 30px; border-radius: 8px; border:none; font-weight: 600; cursor:pointer; transition: 0.2s; }
    .btn-batal:hover { background-color: #555; }
    .btn-kirim { background-color: #e33e42; color: white; padding: 10px 25px; border-radius: 8px; border:none; font-weight: 600; cursor:pointer; transition: 0.2s; }
    .btn-kirim:hover { background-color: #c91a25; }

    /* Responsif */
    @media (max-width: 480px) {
        .floating-checkout { flex-direction: column; gap: 10px; padding: 15px; bottom: -120px; }
        .floating-checkout.visible { bottom: 15px; }
        .checkout-info { text-align: center; }
        .floating-checkout > div:last-child { width: 100%; justify-content: center; gap: 10px; }
        .btn-batal-katalog, .btn-checkout { flex: 1; text-align: center; padding: 10px; font-size: 0.9rem; }
    }
</style>

<div class="catalog-header">
    <h2>Katalog Inventaris</h2>
    <p>Pilih alat atau bahan yang akan anda pinjam hari ini</p>
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
@if(session('error'))
<div style="padding: 15px; background: rgba(227, 62, 66, 0.1); color: #e33e42; border: 1px solid rgba(227, 62, 66, 0.3); border-radius: 8px; margin-bottom: 20px;">
    <strong>Gagal!</strong> {{ session('error') }}
</div>
@endif

<div class="catalog-grid">
    @foreach($items as $item)
    <div class="item-card {{ $item->stock == 0 ? 'disabled' : '' }}" data-category="{{ strtolower(trim($item->category->name)) }}" data-id="{{ $item->id }}" data-stock="{{ $item->stock }}" data-name="{{ $item->name }}">
        @if($item->stock > 0)
        <label class="custom-checkbox-wrapper" onclick="event.stopPropagation();">
            <input type="checkbox" class="item-checkbox" value="{{ $item->id }}" data-category="{{ strtolower(trim($item->category->name)) }}" data-name="{{ $item->name }}" data-stock="{{ $item->stock }}" onchange="handleSelectionChange(this)">
            <div class="checkmark"></div>
        </label>
        @endif

        <div class="img-box" onclick="toggleCardSelection(this)">
            <div class="stock-badge">Tersedia: {{ $item->stock }} {{ $item->unit?->name }}</div>
                <img src="{{ $item->photo_url }}" alt="{{ $item->name }}">
            @if($item->kode_barang)
                <div style="position: absolute; bottom: 10px; left: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-align: center;">
                    {{ $item->kode_barang }}
                </div>
            @endif
        </div>
        
        <div class="card-body" onclick="toggleCardSelection(this)">
            <h3 class="item-name">{{ $item->name }}</h3>
            <p class="item-cat">Lab Komputer | {{ $item->category->name }}</p>
        </div>
    </div>
    @endforeach
</div>

<!-- Floating Checkout Bar -->
<div class="floating-checkout" id="checkoutBar">
    <div class="checkout-info">
        <span class="checkout-count" id="checkoutCountText">0 Barang Terpilih</span>
        <span class="checkout-cat" id="checkoutCatText">-</span>
    </div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-batal-katalog" onclick="clearSelection()">Batal</button>
        <button class="btn-checkout" onclick="openBatchModal()">Lanjut Pinjam</button>
    </div>
</div>

<!-- Modal Batch Request -->
<div class="figma-modal-overlay" id="batchRequestModal">
    <div class="figma-modal">
        <div class="modal-hdr">
            <h3>Keranjang Peminjaman</h3>
            <button type="button" onclick="closeBatchModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666; padding: 0; margin: 0; line-height: 1;">&times;</button>
        </div>
        
        <form action="{{ route('user.requests.store') }}" method="POST" id="batchForm">
            @csrf
            <div class="modal-body">
                <div class="selected-items-list" id="selectedItemsContainer">
                    <!-- Dinamis terisi oleh JS -->
                </div>
                
                <div class="fg-form">
                    <label>Tanggal permintaan</label>
                    <input type="date" name="tanggal" class="fg-input" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="fg-form" style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Jam mulai</label>
                        <input type="time" name="jam_mulai" class="fg-input" value="13:00" required>
                    </div>
                    <div style="flex:1;">
                        <label>Jam selesai</label>
                        <input type="time" name="jam_selesai" class="fg-input" value="15:00" required>
                    </div>
                </div>
                
                <!-- Tanggal Pengembalian (Hanya muncul jika kategori Hardware) -->
                <div id="returnDateSection" style="display:none;">
                    <div class="fg-form">
                        <label>Tanggal pengembalian</label>
                        <input type="date" name="return_date" id="returnDateInput" class="fg-input" min="{{ date('Y-m-d') }}">
                        <p class="note-text">Wajib diisi untuk peminjaman alat (Hardware)</p>
                    </div>
                </div>

                <!-- Pesan Sekali Pakai (Hanya muncul jika kategori Bahan) -->
                <div id="oneTimeSection" style="display:none;">
                    <div class="fg-form">
                        <div style="padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; margin-bottom: 10px;">
                            <strong>📦 Sekali pakai</strong><br>
                            <small>Barang berupa bahan hanya dipakai sesuai permintaan dan tidak perlu dikembalikan.</small>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-batal" onclick="closeBatchModal()">Batal</button>
                <button type="submit" class="btn-kirim">Kirim Permintaan</button>
            </div>
        </form>
    </div>
</div>

<script>
    let selectedCategory = null;

    function toggleCardSelection(element) {
        const card = element.closest('.item-card');
        if (card.classList.contains('disabled')) return;
        
        const checkbox = card.querySelector('.item-checkbox');
        if (checkbox) {
            if (!checkbox.disabled) {
                checkbox.checked = !checkbox.checked;
                handleSelectionChange(checkbox);
            } else if (!checkbox.checked) {
                alert('Anda tidak bisa mencampur barang kategori Hardware dan Bahan dalam satu peminjaman.');
            }
        }
    }

    function handleSelectionChange(checkbox) {
        const card = checkbox.closest('.item-card');
        if (checkbox.checked) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
        
        updateCartState();
    }

    function updateCartState() {
        const checkedBoxes = Array.from(document.querySelectorAll('.item-checkbox:checked'));
        const checkoutBar = document.getElementById('checkoutBar');
        const countText = document.getElementById('checkoutCountText');
        const catText = document.getElementById('checkoutCatText');
        
        if (checkedBoxes.length > 0) {
            // Tentukan kategori dari barang pertama yang dipilih
            selectedCategory = checkedBoxes[0].dataset.category === 'hardware' ? 'hardware' : 'bahan';
            
            // Disable checkbox yang beda kategori
            document.querySelectorAll('.item-checkbox').forEach(box => {
                if (!box.checked) {
                    const boxCat = box.dataset.category === 'hardware' ? 'hardware' : 'bahan';
                    if (boxCat !== selectedCategory) {
                        box.disabled = true;
                        box.closest('.item-card').classList.add('disabled');
                    } else {
                        box.disabled = false;
                        box.closest('.item-card').classList.remove('disabled');
                    }
                }
            });

            countText.textContent = checkedBoxes.length + ' Barang Terpilih';
            catText.textContent = 'Kategori: ' + (selectedCategory === 'hardware' ? 'Hardware (Alat)' : 'Non-Hardware (Bahan)');
            checkoutBar.classList.add('visible');
        } else {
            selectedCategory = null;
            // Enable semua checkbox
            document.querySelectorAll('.item-checkbox').forEach(box => {
                box.disabled = false;
                // Hanya hapus disabled visual jika stoknya ada
                if (parseInt(box.dataset.stock) > 0) {
                    box.closest('.item-card').classList.remove('disabled');
                }
            });
            checkoutBar.classList.remove('visible');
        }
    }

    function openBatchModal() {
        const checkedBoxes = Array.from(document.querySelectorAll('.item-checkbox:checked'));
        if (checkedBoxes.length === 0) return;

        const container = document.getElementById('selectedItemsContainer');
        container.innerHTML = ''; // Kosongkan

        checkedBoxes.forEach((box, index) => {
            const id = box.value;
            const name = box.dataset.name;
            const maxStock = box.dataset.stock;
            
            const row = document.createElement('div');
            row.className = 'selected-item-row';
            row.innerHTML = `
                <div class="s-item-name">${index + 1}. ${name} <br><small style="color:#888;">Max: ${maxStock}</small></div>
                <div class="s-item-qty">
                    <input type="number" name="items[${id}]" min="1" max="${maxStock}" value="1" required>
                </div>
            `;
            container.appendChild(row);
        });

        // Toggle UI Return Date
        const returnSection = document.getElementById('returnDateSection');
        const returnInput = document.getElementById('returnDateInput');
        const oneTimeSection = document.getElementById('oneTimeSection');

        if (selectedCategory === 'hardware') {
            returnSection.style.display = 'block';
            returnInput.required = true;
            oneTimeSection.style.display = 'none';
        } else {
            returnSection.style.display = 'none';
            returnInput.required = false;
            returnInput.value = '';
            oneTimeSection.style.display = 'block';
        }

        document.getElementById('batchRequestModal').style.display = 'flex';
    }

    function closeBatchModal() {
        document.getElementById('batchRequestModal').style.display = 'none';
    }

    function clearSelection() {
        document.querySelectorAll('.item-checkbox').forEach(box => {
            box.checked = false;
        });
        document.querySelectorAll('.item-card').forEach(card => {
            card.classList.remove('selected');
        });
        updateCartState();
    }

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
