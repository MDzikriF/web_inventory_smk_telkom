@extends('layouts.admin')
@section('title', 'Validasi Peminjaman')

@section('content')
<style>
    .val-container {
        display: flex; 
        gap: 20px; 
        flex-wrap: wrap;
    }
    .val-col {
        flex: 1; 
        min-width: 420px;
    }
    @media (max-width: 768px) {
        .val-col {
            min-width: 100%;
        }
        .damage-card {
            flex-direction: column !important;
            align-items: stretch !important;
        }
        .damage-img-container {
            width: 100% !important;
            height: 200px !important;
        }
        .damage-actions {
            flex-direction: row;
            width: 100%;
            justify-content: space-between;
        }
        .damage-actions button {
            flex: 1;
        }
    }
</style>

<h1 class="page-title">Validasi Peminjaman & Kerusakan</h1>

<div class="val-container">
    <div class="val-col">
        <div class="card">
            <div style="margin-bottom: 20px;">
                <h3 style="font-weight: 600; color: var(--text-dark);">Antrean Permintaan</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Daftar permohonan peminjaman yang menunggu persetujuan Anda.</p>
            </div>

            @if($requests->isEmpty())
                <div style="text-align: center; padding: 40px; color: var(--text-muted); background: #fdfdfd; border-radius: 8px; border: 1px dashed var(--border-color);">
                    Tidak ada permohonan yang menunggu validasi saat ini.
                </div>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Tgl Pengajuan</th>
                                <th>Pemohon</th>
                                <th>Detail Barang</th>
                                <th>Periode</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                            <tr>
                                <td style="color: var(--text-muted);">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y, H:i') }}</td>
                                <td>
                                    <strong>{{ optional($req->user)->name ?? $req->reporter_name ?? 'User Dihapus' }}</strong><br>
                                    <span style="font-size:0.85rem; color:var(--text-muted);">{{ optional($req->user)->email ?? $req->reporter_email ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <ul style="padding-left: 15px; margin: 0; font-size: 0.9rem;">
                                        @foreach($req->details as $detail)
                                            <li>{{ $detail->quantity }}x {{ $detail->item->name ?? 'Barang Dihapus' }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem; font-weight: 600;">{{ \Carbon\Carbon::parse($req->request_date)->format('d M') }}</span>
                                    s/d
                                    <span style="font-size: 0.85rem; font-weight: 600;">{{ \Carbon\Carbon::parse($req->return_date)->format('d M') }}</span>
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex; flex-direction:column; gap:5px; align-items:center;">
                                        <button type="button" onclick="openModal('reqModal{{$req->id}}')" class="btn" style="background:transparent; color:var(--primary); border:1px solid var(--primary); padding: 4px 10px; font-size: 0.8rem; width: 100%; border-radius: 6px; font-weight: 600;">Lihat Detail</button>
                                        @if($req->status === 'pending')
                                            <form action="{{ route('admin.validations.approve', $req->id) }}" method="POST" onsubmit="return confirm('Setujui peminjaman ini? Stok inventaris akan berkurang otomatis.');" style="width:100%;">
                                                @csrf
                                                <button type="submit" class="btn" style="background:#27ae60; color:white; padding: 6px 12px; font-size: 0.85rem; width: 100%;">Terima</button>
                                            </form>
                                            <form action="{{ route('admin.validations.reject', $req->id) }}" method="POST" onsubmit="return confirm('Tolak peminjaman ini?');" style="width:100%;">
                                                @csrf
                                                <button type="submit" class="btn" style="background:#e74c3c; color:white; padding: 6px 12px; font-size: 0.85rem; width: 100%;">Tolak</button>
                                            </form>
                                        @elseif($req->status === 'return_requested')
                                            <form action="{{ route('admin.history.return', $req->id) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa barang fisik telah dikembalikan? Stok akan dipulihkan otomatis.');" style="width:100%;">
                                                @csrf
                                                <button type="submit" class="btn" style="background:#1d4ed8; color:white; padding: 6px 12px; font-size: 0.85rem; width: 100%;">Konfirmasi Kembali</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            
                            @push('modals')
                            <div class="modal-overlay" id="reqModal{{$req->id}}">
                                <div class="modal" style="max-width: 500px; text-align:left;">
                                    <div class="modal-header">
                                        <h3 style="font-weight: 700;">Detail Permohonan Peminjaman</h3>
                                        <button class="close-modal" type="button" onclick="closeModal('reqModal{{$req->id}}')">&times;</button>
                                    </div>
                                    <div style="display:flex; flex-direction:column; gap:15px;">
                                        <div>
                                            <h4 style="margin:0 0 5px 0; color:var(--text-dark); font-size: 0.9rem; text-transform: uppercase;">Informasi Pemohon</h4>
                                            <p style="margin:0; font-size:1.05rem; font-weight: 600;">{{ optional($req->user)->name ?? $req->reporter_name ?? 'User Dihapus' }}</p>
                                            <p style="margin:0; font-size:0.9rem; color:var(--text-muted);">{{ optional($req->user)->email ?? $req->reporter_email ?? 'N/A' }}</p>
                                            <p style="margin:5px 0 0 0; font-size:0.85rem; color:var(--text-muted);">Waktu Pengajuan: {{ \Carbon\Carbon::parse($req->created_at)->format('d M Y, H:i') }}</p>
                                        </div>
                                        <div>
                                            <h4 style="margin:0 0 5px 0; color:var(--text-dark); font-size: 0.9rem; text-transform: uppercase;">Daftar Barang</h4>
                                            <ul style="padding-left: 20px; margin: 0; font-size: 1rem; color: #334155;">
                                                @foreach($req->details as $detail)
                                                    <li style="margin-bottom: 5px;"><strong>{{ $detail->quantity }}x</strong> {{ $detail->item->name ?? 'Barang Dihapus' }} <span style="font-size: 0.85rem; color: var(--text-muted);">(Kode: {{ $detail->item->kode_barang ?? '-' }})</span></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div>
                                            <h4 style="margin:0 0 5px 0; color:var(--text-dark); font-size: 0.9rem; text-transform: uppercase;">Waktu / Periode Penggunaan</h4>
                                            <div style="background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
                                                <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                                                    <span style="color:var(--text-muted); font-size:0.9rem;">Mulai:</span>
                                                    <strong style="color:var(--text-dark);">{{ \Carbon\Carbon::parse($req->request_date)->format('d M Y, H:i') }}</strong>
                                                </div>
                                                <div style="display:flex; justify-content:space-between;">
                                                    <span style="color:var(--text-muted); font-size:0.9rem;">Selesai:</span>
                                                    <strong style="color:var(--text-dark);">{{ $req->return_date ? \Carbon\Carbon::parse($req->return_date)->format('d M Y, H:i') : 'Sekali Pakai' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 style="margin:0 0 5px 0; color:var(--text-dark); font-size: 0.9rem; text-transform: uppercase;">Catatan Pemohon</h4>
                                            <div style="background:#f1f5f9; padding:12px; border-radius:8px; font-size:0.95rem; color:#334155; white-space:pre-wrap;">{{ $req->notes ?? 'Tidak ada catatan' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endpush
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="val-col">
        <div class="card">
            <div style="margin-bottom: 20px;">
                <h3 style="font-weight: 600; color: var(--text-dark);">Antrean Laporan Kerusakan</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Laporan kerusakan aset dari user yang sedang menunggu penanganan.</p>
            </div>

            @if($damageReports->isEmpty())
                <div style="text-align: center; padding: 40px; color: var(--text-muted); background: #fdfdfd; border-radius: 8px; border: 1px dashed var(--border-color);">
                    Tidak ada laporan kerusakan yang menunggu tinjauan.
                </div>
            @else
                <div style="display:flex; flex-direction:column; gap:16px;">
                    @foreach($damageReports as $report)
                        <div class="damage-card" style="display:flex; gap:16px; padding:18px 16px; border:1px solid #e5e7eb; border-radius:16px; background:#fff; align-items:flex-start; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);">
                            @if($report->photo)
                                <div class="damage-img-container" style="flex-shrink:0; width:96px; height:96px; border-radius:16px; overflow:hidden; background:#f8fafc; display:flex; align-items:center; justify-content:center; border:1px solid #e2e8f0;">
                                    <img src="{{ asset($report->photo) }}" alt="Foto Kerusakan" style="width:100%; height:100%; object-fit:cover;" />
                                </div>
                            @endif
                            <div style="flex:1; min-width:0; display:flex; flex-direction:column; gap:10px;">
                                <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
                                    <div style="min-width:0;">
                                        <div style="font-size:1rem; font-weight:700; color: var(--text-dark);">{{ $report->item->name ?? 'Aset Dihapus' }}</div>
                                        <div style="font-size:0.85rem; color: var(--text-muted); margin-top:4px;">{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y') }}</div>
                                    </div>
                                    <div>
                                        @if($report->status == 'pending')
                                            <span style="display:inline-block; padding: 6px 10px; border-radius:999px; background: rgba(250, 204, 21, 0.15); color: #b45309; font-size: 0.8rem; font-weight:700;">Menunggu</span>
                                        @elseif($report->status == 'reviewed')
                                            <span style="display:inline-block; padding: 6px 10px; border-radius:999px; background: rgba(59, 130, 246, 0.15); color: #1d4ed8; font-size: 0.8rem; font-weight:700;">Ditinjau</span>
                                        @elseif($report->status == 'unrepairable')
                                            <span style="display:inline-block; padding: 6px 10px; border-radius:999px; background: rgba(239, 68, 68, 0.15); color: #b91c1c; font-size: 0.8rem; font-weight:700;">Rusak</span>
                                        @else
                                            <span style="display:inline-block; padding: 6px 10px; border-radius:999px; background: rgba(16, 185, 129, 0.15); color: #047857; font-size: 0.8rem; font-weight:700;">Selesai</span>
                                        @endif
                                    </div>
                                </div>
                                <div style="display:grid; gap:10px;">
                                    <div style="font-size:0.9rem; color: var(--text-muted);">
                                        {{ Str::limit($report->notes, 90) }}
                                        <button type="button" onclick="openModal('detailModal{{$report->id}}')" style="background:none; border:none; color:var(--primary); font-weight:700; font-size:0.85rem; padding:0; cursor:pointer; margin-top:6px; display:inline-block;">Lihat Detail Laporan &rarr;</button>
                                    </div>
                                    <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:center;">
                                        <div>
                                            <div style="font-size:0.78rem; color: var(--text-muted); text-transform: uppercase; letter-spacing:0.03em;">Pengirim</div>
                                            <div style="font-weight:600;">{{ optional($report->user)->name ?? $report->reporter_name ?? 'User Dihapus' }}</div>
                                            <div style="font-size:0.85rem; color: var(--text-muted);">{{ optional($report->user)->email ?? $report->reporter_email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="damage-actions" style="display:flex; gap:10px; align-items:flex-start; flex-wrap: wrap;">
                                @if($report->status == 'pending' || $report->status == 'reviewed')
                                    <form action="{{ route('admin.validations.damage.resolve', $report->id) }}" method="POST" onsubmit="return confirm('Tandai laporan ini selesai diperbaiki?');" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="btn" style="background:#10b981; color:white; padding: 10px 14px; font-size: 0.88rem; border-radius: 10px;">Selesai Diperbaiki</button>
                                    </form>
                                    <form action="{{ route('admin.validations.damage.unrepairable', $report->id) }}" method="POST" onsubmit="return confirm('Tandai barang ini rusak / tidak bisa diperbaiki?');" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="btn" style="background:#ef4444; color:white; padding: 10px 14px; font-size: 0.88rem; border-radius: 10px;">Barang Rusak</button>
                                    </form>
                                @endif
                            </div>

                            @push('modals')
                            <div class="modal-overlay" id="detailModal{{$report->id}}">
                                <div class="modal" style="max-width: 600px;">
                                    <div class="modal-header">
                                        <h3 style="font-weight: 700;">Detail Laporan Kerusakan</h3>
                                        <button class="close-modal" type="button" onclick="closeModal('detailModal{{$report->id}}')">&times;</button>
                                    </div>
                                    <div style="display:flex; flex-direction:column; gap:20px;">
                                        @if($report->photo)
                                            <div style="width:100%; border-radius:12px; overflow:hidden; background:#f8fafc; border:1px solid #e2e8f0;">
                                                <img src="{{ asset($report->photo) }}" alt="Foto Kerusakan" style="width:100%; max-height:400px; object-fit:contain; display:block;" />
                                            </div>
                                        @endif
                                        <div>
                                            <h4 style="margin:0 0 5px 0; color:var(--text-dark); font-size: 0.9rem; text-transform: uppercase;">Aset yang Dilaporkan</h4>
                                            <p style="margin:0; font-size:1.1rem; font-weight: 600;">{{ $report->item->name ?? 'Aset Dihapus' }} <span style="font-size: 0.9rem; font-weight: normal; color: var(--text-muted);">(Kode: {{ $report->item->kode_barang ?? '-' }})</span></p>
                                        </div>
                                        <div>
                                            <h4 style="margin:0 0 5px 0; color:var(--text-dark); font-size: 0.9rem; text-transform: uppercase;">Informasi Pelapor</h4>
                                            <p style="margin:0; font-size:1rem; font-weight: 500;">{{ optional($report->user)->name ?? $report->reporter_name ?? 'User Dihapus' }}</p>
                                            <p style="margin:0; font-size:0.9rem; color:var(--text-muted);">{{ optional($report->user)->email ?? $report->reporter_email ?? 'N/A' }}</p>
                                            <p style="margin:5px 0 0 0; font-size:0.85rem; color:var(--text-muted);">Waktu Lapor: {{ \Carbon\Carbon::parse($report->created_at)->format('d M Y, H:i') }}</p>
                                        </div>
                                        <div>
                                            <h4 style="margin:0 0 5px 0; color:var(--text-dark); font-size: 0.9rem; text-transform: uppercase;">Catatan / Kronologi</h4>
                                            <div style="background:#f1f5f9; padding:15px; border-radius:8px; font-size:0.95rem; color:#334155; white-space:pre-wrap; line-height: 1.5;">{{ $report->notes }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endpush
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
