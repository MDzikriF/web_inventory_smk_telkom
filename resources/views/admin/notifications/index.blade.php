@extends('layouts.admin')

@section('title', 'Histori & Notifikasi')

@section('content')
<style>
    /* Tabs System */
    .nf-tabs { display: flex; gap: 20px; border-bottom: 2px solid #eee; margin-bottom: 25px; padding-bottom: 10px; }
    .nf-tab-btn { background: none; border: none; font-size: 1rem; font-weight: 700; color: #777; cursor: pointer; padding-bottom: 5px; position:relative; }
    .nf-tab-btn.active { color: #dc3545; }
    .nf-tab-btn.active::after { content:''; position:absolute; bottom: -12px; left:0; width:100%; height:3px; background-color:#dc3545; border-radius:3px; }

    .nf-tab-content { display: none; }
    .nf-tab-content.active { display: block; }

    /* Subtabs */
    .nf-subtabs { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .nf-subtab-btn { background: white; border: 1px solid #ddd; padding: 6px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; color: #555; cursor: pointer; transition: 0.2s; }
    .nf-subtab-btn.active { background: #dc3545; color: white; border-color: #dc3545; }
    .nf-subtab-btn:hover:not(.active) { background: #fdfdfd; border-color: #aaa; }
    
    .nf-subtab-content { display: none; }
    .nf-subtab-content.active { display: block; }
    
    /* Notifikasi List */
    .nf-list { display: flex; flex-direction: column; gap: 15px; }
    .nf-item { display: flex; align-items: flex-start; gap: 15px; background: white; padding: 15px; border-radius: 12px; border: 1px solid #eee; transition: 0.2s; }
    .nf-item:hover { box-shadow: 0 4px 10px rgba(0,0,0,0.03); transform: translateY(-2px); }
    .nf-icon { width: 45px; height: 45px; background-color: #f8f9fa; border-radius: 12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.2rem; }
    .nf-body { flex: 1; }
    .nf-title { font-weight: 700; font-size: 1rem; color: #333; margin:0 0 5px; }
    .nf-desc { font-size: 0.85rem; color: #666; margin:0 0 8px; line-height:1.4; }
    .nf-time { font-size: 0.75rem; color: #999; font-weight:600; }
    .btn-mark-read { background: rgba(220, 53, 69, 0.1); color: #dc3545; border: none; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: 0.2s; white-space:nowrap; }
    .btn-mark-read:hover { background: #dc3545; color: white; }
    .unread { border-left: 4px solid #dc3545; background-color: #fff9fa; }
    
    /* Pagination Fix */
    .custom-pagination { display: flex; justify-content: center; margin-top: 20px; }
    .custom-pagination nav { display: flex; align-items: center; justify-content: space-between; width: 100%; font-size: 0.9rem; flex-wrap: wrap; gap: 15px; }
    .custom-pagination ul.pagination { display: flex; padding-left: 0; list-style: none; gap: 5px; margin: 0; }
    .custom-pagination .page-link { position: relative; display: block; padding: 6px 12px; border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-dark); text-decoration: none; background: white; transition: 0.2s; font-weight: 500; }
    .custom-pagination .page-link:hover { background: #f8f9fa; border-color: #ccc; }
    .custom-pagination .page-item.active .page-link { z-index: 3; color: #fff; background-color: var(--primary); border-color: var(--primary); }
    .custom-pagination .page-item.disabled .page-link { color: var(--text-muted); pointer-events: none; background-color: #f8f9fa; border-color: var(--border-color); opacity: 0.6; }
</style>

<div class="nf-tabs">
    <button class="nf-tab-btn active" onclick="switchTab('notifikasi', this)">Pusat Notifikasi</button>
    <button class="nf-tab-btn" onclick="switchTab('history', this)">History Peminjaman</button>
</div>

<!-- TAB NOTIFIKASI -->
<div id="tab-notifikasi" class="nf-tab-content active">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Notifikasi Masuk</h4>
        </div>
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="nf-list">
                    @foreach($notifications as $notification)
                        <div class="nf-item {{ $notification->is_read ? '' : 'unread' }}">
                            <div class="nf-body">
                                <h4 class="nf-title">{{ $notification->title }}</h4>
                                <p class="nf-desc">{{ $notification->message }}</p>
                                <div class="nf-time">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                            @if(!$notification->is_read)
                                <div style="display:flex; align-items:center;">
                                    <button class="btn-mark-read mark-read" data-id="{{ $notification->id }}">
                                        Tandai Dibaca
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 custom-pagination">
                    {{ $notifications->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="text-muted mb-3">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <h5 class="text-muted">Tidak ada notifikasi</h5>
                    <p class="text-muted">Semua notifikasi akan muncul di sini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- TAB HISTORY -->
<div id="tab-history" class="nf-tab-content">
    <div class="card">
        <div style="margin-bottom: 20px;">
            <h3 style="font-weight: 600; color: var(--text-dark);">Pusat Laporan & Histori</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Daftar aktivitas peminjaman, keluar/masuk barang, dan perbaikan aset.</p>
        </div>

        <div class="nf-subtabs">
            <button class="nf-subtab-btn active" onclick="switchSubTab('peminjaman', this)">Peminjaman Aset</button>
            <button class="nf-subtab-btn" onclick="switchSubTab('keluarmasuk', this)">Keluar Masuk Stok</button>
            <button class="nf-subtab-btn" onclick="switchSubTab('perbaikan', this)">Laporan Perbaikan</button>
            <button class="nf-subtab-btn" onclick="switchSubTab('cetak', this)" style="background-color: #f8f9fa; border-color: #ddd; color: #333;">Cetak Laporan</button>
        </div>

        <!-- SUBTAB: PEMINJAMAN -->
        <div id="subtab-peminjaman" class="nf-subtab-content active">

        @if($histories->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--text-muted); background: #fdfdfd; border-radius: 8px; border: 1px dashed var(--border-color);">
                Belum ada riwayat transaksi.
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tgl Update Terakhir</th>
                            <th>Peminjam</th>
                            <th>Barang Keluar</th>
                            <th>Periode/Keterangan</th>
                            <th>Kondisi Status</th>
                            <th style="text-align:center;">Manajemen Fisik</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($histories as $hist)
                        <tr>
                            <td style="color: var(--text-muted);">{{ \Carbon\Carbon::parse($hist->updated_at)->format('d M Y, H:i') }}</td>
                            <td>
                                <strong>{{ $hist->user->name ?? 'User Dihapus' }}</strong><br>
                                <span style="font-size:0.85rem; color:var(--text-muted);">Tgl Ajuan: {{ \Carbon\Carbon::parse($hist->created_at)->format('d M Y') }}</span>
                            </td>
                            <td>
                                <ul style="padding-left: 15px; margin: 0; font-size: 0.9rem;">
                                    @foreach($hist->details as $detail)
                                        <li>{{ $detail->quantity }}x {{ $detail->item->name ?? 'Barang Dihapus' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                @php
                                    $firstItem = $hist->details->first();
                                    $categoryName = $firstItem && $firstItem->item ? $firstItem->item->category->name : 'Unknown';
                                @endphp
                                
                                @if($categoryName === 'Alat' && $hist->return_date)
                                    <span style="font-size: 0.85rem; font-weight: 600;">{{ \Carbon\Carbon::parse($hist->request_date)->format('d M') }}</span>
                                    s/d
                                    <span style="font-size: 0.85rem; font-weight: 600;">{{ \Carbon\Carbon::parse($hist->return_date)->format('d M') }}</span>
                                    <br><small style="color: var(--text-muted);">Kategori: Alat</small>
                                @elseif($categoryName === 'Bahan')
                                    <span style="font-size: 0.85rem; color: #e67e22; font-weight: 600;">Bahan Habis Pakai</span>
                                    <br><small style="color: var(--text-muted);">Tidak perlu dikembalikan</small>
                                @else
                                    <span style="font-size: 0.85rem; color: var(--text-muted);">Kategori: {{ $categoryName }}</span>
                                @endif
                            </td>
                            <td>
                                @if($hist->status === 'approved')
                                    <span style="background: rgba(243, 156, 18, 0.1); color: #e67e22; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Sedang Dipinjam</span>
                                @elseif($hist->status === 'return_requested')
                                    <span style="background: rgba(52, 152, 219, 0.1); color: #2980b9; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Menunggu Konfirmasi Pengembalian</span>
                                @elseif($hist->status === 'returned')
                                    <span style="background: rgba(46, 204, 113, 0.1); color: #27ae60; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Selesai/Kembali</span>
                                @elseif($hist->status === 'rejected')
                                    <span style="background: rgba(231, 76, 60, 0.1); color: #c0392b; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Ditolak</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if(in_array($hist->status, ['approved', 'return_requested']) && $categoryName === 'Alat')
                                    <form action="{{ route('admin.history.return', $hist->id) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa barang fisik telah Anda terima kembali? Stok akan dipulihkan otomatis.');">
                                        @csrf
                                        <button type="submit" class="btn" style="background:var(--primary); color:white; font-size: 0.85rem; padding: 6px 12px; border:none; border-radius:6px; cursor:pointer;">✅ Konfirmasi Pengembalian</button>
                                    </form>
                                @elseif($hist->status === 'approved' && $categoryName === 'Bahan')
                                    <span style="color: #e67e22; font-size: 0.85rem; font-weight: 600;">📦 Bahan Habis Pakai</span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">- Tidak Ada Aksi -</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>
        @endif
        </div> <!-- End Subtab Peminjaman -->

        <!-- SUBTAB: KELUAR MASUK STOK -->
        <div id="subtab-keluarmasuk" class="nf-subtab-content">
            @if($transactions->isEmpty())
                <div style="text-align: center; padding: 40px; color: var(--text-muted); background: #fdfdfd; border-radius: 8px; border: 1px dashed var(--border-color);">
                    Belum ada riwayat stok keluar atau masuk.
                </div>
            @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tgl Update Terakhir</th>
                            <th>Tipe Translasi</th>
                            <th>Barang</th>
                            <th style="text-align:center;">Qty</th>
                            <th>Keterangan Tambahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $trans)
                        <tr>
                            <td style="color: var(--text-muted);">{{ \Carbon\Carbon::parse($trans->created_at)->format('d M Y, H:i') }}</td>
                            <td>
                                @if($trans->type == 'in')
                                    <span style="background: rgba(46, 204, 113, 0.1); color: #27ae60; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">⬇️ Masuk (In)</span>
                                @else
                                    <span style="background: rgba(231, 76, 60, 0.1); color: #c0392b; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">⬆️ Keluar (Out)</span>
                                @endif
                                <br><small style="color: var(--text-muted);">Trx ID: {{ $trans->id }}</small>
                            </td>
                            <td><strong>{{ $trans->item->name ?? 'Barang Dihapus' }}</strong></td>
                            <td style="text-align:center; font-weight:700;">{{ $trans->quantity ?? '-' }} x</td>
                            <td>{{ $trans->notes ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- SUBTAB: PERBAIKAN -->
        <div id="subtab-perbaikan" class="nf-subtab-content">
            @if($damageReports->isEmpty())
                <div style="text-align: center; padding: 40px; color: var(--text-muted); background: #fdfdfd; border-radius: 8px; border: 1px dashed var(--border-color);">
                    Belum ada laporan kerusakan/perbaikan barang.
                </div>
            @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tgl Dilaporkan</th>
                            <th>Pelapor</th>
                            <th>Barang</th>
                            <th>Catatan Kerusakan</th>
                            <th>Status Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($damageReports as $report)
                        <tr>
                            <td style="color: var(--text-muted);">{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y, H:i') }}</td>
                            <td><strong>{{ $report->user->name ?? 'User Dihapus' }}</strong><br><small style="color:var(--text-muted);">NIP/NIS: {{ $report->user_id }}</small></td>
                            <td><strong>{{ $report->item->name ?? 'Barang Dihapus' }}</strong><br><small style="color:var(--text-muted);">Tiket ID: {{ $report->id }}</small></td>
                            <td style="max-width: 250px;">{{ Str::limit($report->notes, 75) }}</td>
                            <td>
                                @if($report->status == 'pending')
                                    <span style="background: rgba(243, 156, 18, 0.1); color: #f39c12; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Menunggu Review</span>
                                @elseif($report->status == 'reviewed')
                                    <span style="background: rgba(52, 152, 219, 0.1); color: #2980b9; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">⏱️ Dalam Perbaikan</span>
                                @elseif($report->status == 'resolved')
                                    <span style="background: rgba(46, 204, 113, 0.1); color: #27ae60; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">✅ Selesai Diperbaiki</span>
                                @else
                                    <span style="background: rgba(149, 165, 166, 0.1); color: #7f8c8d; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">{{ $report->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- SUBTAB: CETAK LAPORAN -->
        <div id="subtab-cetak" class="nf-subtab-content">
            <div style="background: #fdfdfd; border: 1px solid #eee; border-radius: 12px; padding: 25px;">
                <h4 style="margin-top: 0; font-weight: 700; color: #333; margin-bottom: 15px;">Ekspor Laporan Bulanan</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">
                    Pilih bulan dan tahun untuk menghasilkan laporan Keluar/Masuk Stok dan Kerusakan Barang. Anda dapat mencetaknya langsung ke PDF atau mengunduh dalam format Excel (CSV).
                </p>
                
                <form id="formCetakLaporan" method="GET" action="{{ route('admin.history.report.pdf') }}" target="_blank" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                    
                    <div style="flex: 1; min-width: 150px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 5px;">Bulan</label>
                        <select name="bulan" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-family: inherit;">
                            @php
                                $bulanSekarang = date('n');
                                $bulanList = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @foreach($bulanList as $num => $name)
                                <option value="{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}" {{ $bulanSekarang == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="flex: 1; min-width: 150px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 5px;">Tahun</label>
                        <select name="tahun" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-family: inherit;">
                            @php
                                $tahunSekarang = date('Y');
                            @endphp
                            @for($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px; padding-top: 5px;">
                        <button type="submit" onclick="document.getElementById('formCetakLaporan').action='{{ route('admin.history.report.pdf') }}'; document.getElementById('formCetakLaporan').target='_blank';" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                            Cetak PDF
                        </button>
                        <button type="submit" onclick="document.getElementById('formCetakLaporan').action='{{ route('admin.history.report.excel') }}'; document.getElementById('formCetakLaporan').target='';" style="background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                            Export Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId, btn) {
        document.querySelectorAll('.nf-tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.nf-tab-btn').forEach(el => el.classList.remove('active'));
        
        document.getElementById('tab-' + tabId).classList.add('active');
        btn.classList.add('active');
    }

    function switchSubTab(subId, btn) {
        document.querySelectorAll('.nf-subtab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.nf-subtab-btn').forEach(el => el.classList.remove('active'));
        
        document.getElementById('subtab-' + subId).classList.add('active');
        btn.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.mark-read').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var btn = this;
                
                fetch('{{ url("admin/notifications") }}/' + id + '/read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = btn.closest('.nf-item');
                        if (item) item.classList.remove('unread');
                        btn.remove();
                        const badge = document.getElementById('adminNotificationBadge');
                        if (badge) {
                            const count = parseInt(badge.innerText || '0', 10) - 1;
                            badge.innerText = count > 0 ? count : '';
                            if (count <= 0) badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
</script>
@endsection