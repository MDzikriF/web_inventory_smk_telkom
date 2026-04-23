@extends('layouts.admin')
@section('title', 'Validasi Peminjaman')

@section('content')
<h1 class="page-title">Validasi Peminjaman & Kerusakan</h1>

<div style="display:flex; gap:20px; flex-wrap:wrap;">
    <div style="flex:1; min-width:420px;">
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
                                        @if($req->status === 'pending')
                                            <form action="{{ route('admin.validations.approve', $req->id) }}" method="POST" onsubmit="return confirm('Setujui peminjaman ini? Stok inventaris akan berkurang otomatis.');">
                                                @csrf
                                                <button type="submit" class="btn" style="background:#27ae60; color:white; padding: 6px 12px; font-size: 0.85rem; width: 80px;">Terima</button>
                                            </form>
                                            <form action="{{ route('admin.validations.reject', $req->id) }}" method="POST" onsubmit="return confirm('Tolak peminjaman ini?');">
                                                @csrf
                                                <button type="submit" class="btn" style="background:#e74c3c; color:white; padding: 6px 12px; font-size: 0.85rem; width: 80px;">Tolak</button>
                                            </form>
                                        @elseif($req->status === 'return_requested')
                                            <form action="{{ route('admin.history.return', $req->id) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa barang fisik telah dikembalikan? Stok akan dipulihkan otomatis.');">
                                                @csrf
                                                <button type="submit" class="btn" style="background:#1d4ed8; color:white; padding: 6px 12px; font-size: 0.85rem; width: 140px;">Konfirmasi Pengembalian</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div style="flex:1; min-width:420px;">
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
                        <div style="display:flex; gap:16px; padding:18px 16px; border:1px solid #e5e7eb; border-radius:16px; background:#fff; align-items:flex-start; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);">
                            @if($report->photo)
                                <div style="flex-shrink:0; width:96px; height:96px; border-radius:16px; overflow:hidden; background:#f8fafc; display:flex; align-items:center; justify-content:center; border:1px solid #e2e8f0;">
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
                                        @else
                                            <span style="display:inline-block; padding: 6px 10px; border-radius:999px; background: rgba(16, 185, 129, 0.15); color: #047857; font-size: 0.8rem; font-weight:700;">Selesai</span>
                                        @endif
                                    </div>
                                </div>
                                <div style="display:grid; gap:10px;">
                                    <div style="font-size:0.9rem; color: var(--text-muted);">{{ Str::limit($report->notes, 100) }}</div>
                                    <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:center;">
                                        <div>
                                            <div style="font-size:0.78rem; color: var(--text-muted); text-transform: uppercase; letter-spacing:0.03em;">Pengirim</div>
                                            <div style="font-weight:600;">{{ optional($report->user)->name ?? $report->reporter_name ?? 'User Dihapus' }}</div>
                                            <div style="font-size:0.85rem; color: var(--text-muted);">{{ optional($report->user)->email ?? $report->reporter_email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="display:flex; flex-direction:column; gap:10px; align-items:flex-end;">
                                @if($report->status == 'pending')
                                    <form action="{{ route('admin.validations.damage.review', $report->id) }}" method="POST" onsubmit="return confirm('Tandai laporan ini sebagai sedang ditinjau?');">
                                        @csrf
                                        <button type="submit" class="btn" style="background:#f59e0b; color:white; padding: 10px 14px; font-size: 0.88rem; border-radius: 10px;">Tinjau</button>
                                    </form>
                                @elseif($report->status == 'reviewed')
                                    <form action="{{ route('admin.validations.damage.resolve', $report->id) }}" method="POST" onsubmit="return confirm('Tandai laporan ini selesai?');">
                                        @csrf
                                        <button type="submit" class="btn" style="background:#10b981; color:white; padding: 10px 14px; font-size: 0.88rem; border-radius: 10px;">Selesai</button>
                                    </form>
                                @else
                                    <div style="font-size:0.85rem; color: var(--text-muted);">Tidak ada aksi</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
