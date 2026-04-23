@extends('layouts.user')
@section('title', 'Histori Peminjaman')

@section('content')
<h1 class="page-title">Riwayat Peminjaman Anda</h1>

<div class="card">
    <div style="margin-bottom: 20px;">
        <h3 style="font-weight: 600; color: var(--text-dark);">Daftar Aset yang Dipinjam</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Informasi alat yang sedang berada di tangan Anda atau sudah dikembalikan.</p>
    </div>

    @if($histories->isEmpty())
        <div style="text-align:center; padding: 50px 20px; color: var(--text-muted);">
            <span style="font-size: 3rem; display:block; margin-bottom:10px;">🕒</span>
            Belum ada riwayat peminjaman.
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Tgl Pengajuan</th>
                        <th>Barang dipinjam</th>
                        <th style="text-align:center;">Qty</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($histories as $detail)
                        @php
                            $req = $detail->itemRequest;
                            $isBahan = strtolower($detail->item->category->name ?? '') === 'bahan';
                        @endphp
                        <tr style="{{ (!$isBahan && in_array($req->status, ['pending', 'approved', 'return-requested'])) ? 'background-color: rgba(52, 152, 219, 0.03);' : '' }}">
                            <td style="color:var(--text-muted);">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}</td>
                            <td>
                                <strong>{{ $detail->item->name ?? 'Aset Dihapus' }}</strong><br>
                                <span style="font-size:0.8rem; color:var(--text-muted);">Req ID: #{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td style="text-align:center; font-weight:700;">{{ $detail->quantity }} {{ $detail->item?->unit?->name }}</td>
                            <td>
                                @if($req->status == 'rejected')
                                    <span style="display:inline-block; padding: 4px 10px; border-radius: 4px; background: rgba(231, 76, 60, 0.1); color: #e74c3c; font-weight:600; font-size:0.85rem;">❌ Ditolak</span>
                                @elseif($isBahan)
                                    {{-- Untuk Bahan, jika disetujui, diasumsikan langsung habis pakai tanpa butuh return --}}
                                    @if($req->status == 'pending')
                                        <span style="display:inline-block; padding: 4px 10px; border-radius: 4px; background: rgba(241, 196, 15, 0.1); color: #f39c12; font-weight:600; font-size:0.85rem;">⏳ Menyesuaikan Stok...</span>
                                    @else
                                        <span style="display:inline-block; padding: 4px 10px; border-radius: 4px; background: #f8f9fa; border: 1px solid #ddd; color: #7f8c8d; font-weight:600; font-size:0.85rem;">📦 Barang Habis Pakai (Selesai)</span>
                                    @endif
                                @else
                                    {{-- Untuk Alat --}}
                                    @if($req->status == 'approved')
                                        <span style="display:inline-block; padding: 4px 10px; border-radius: 4px; background: rgba(52, 152, 219, 0.2); color: #2980b9; border: 1px solid #3498db; font-weight:600; font-size:0.85rem;">🔵 Sedang Dipinjam</span>
                                    @elseif($req->status == 'returned')
                                        <span style="display:inline-block; padding: 4px 10px; border-radius: 4px; background: rgba(39, 174, 96, 0.1); color: #27ae60; font-weight:600; font-size:0.85rem;">✅ Selesai Dikembalikan</span>
                                    @elseif($req->status == 'return-requested')
                                        <span style="display:inline-block; padding: 4px 10px; border-radius: 4px; background: rgba(241, 196, 15, 0.1); color: #e67e22; font-weight:600; font-size:0.85rem;">⏳ Menunggu Konfirmasi Admin</span>
                                    @else
                                        <span style="display:inline-block; padding: 4px 10px; border-radius: 4px; background: rgba(241, 196, 15, 0.1); color: #f39c12; font-weight:600; font-size:0.85rem;">⏳ Menunggu Persetujuan</span>
                                    @endif
                                @endif
                                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:5px;">
                                    {{ Str::limit($req->notes, 35) }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
