@extends('layouts.admin')

@section('title', 'Cetak Laporan Bulanan')

@section('content')
<h1 class="page-title">Laporan Bulanan</h1>

<div class="card" style="margin-bottom: 20px;">
    <div style="background: #fdfdfd; border: 1px solid #eee; border-radius: 12px; padding: 25px;">
        <h4 style="margin-top: 0; font-weight: 700; color: #333; margin-bottom: 15px;">Ekspor Laporan Peminjaman Aset</h4>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">
            Pilih bulan dan tahun untuk menghasilkan laporan Peminjaman Aset. Anda dapat mencetaknya langsung ke PDF atau mengunduh dalam format Excel (CSV).
        </p>
        
        <form id="formPeminjaman" method="GET" action="{{ route('admin.history.report.peminjaman_pdf') }}" target="_blank" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            
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
                <button type="submit" onclick="document.getElementById('formPeminjaman').action='{{ route('admin.history.report.peminjaman_pdf') }}'; document.getElementById('formPeminjaman').target='_blank';" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                    Cetak PDF
                </button>
                <button type="submit" onclick="document.getElementById('formPeminjaman').action='{{ route('admin.history.report.peminjaman_excel') }}'; document.getElementById('formPeminjaman').target='';" style="background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                    Export Excel
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card" style="margin-bottom: 20px;">
    <div style="background: #fdfdfd; border: 1px solid #eee; border-radius: 12px; padding: 25px;">
        <h4 style="margin-top: 0; font-weight: 700; color: #333; margin-bottom: 15px;">Ekspor Laporan Keluar Masuk Stok</h4>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">
            Pilih bulan dan tahun untuk menghasilkan laporan Keluar/Masuk Stok. Anda dapat mencetaknya langsung ke PDF atau mengunduh dalam format Excel (CSV).
        </p>
        
        <form id="formStok" method="GET" action="{{ route('admin.history.report.stok_pdf') }}" target="_blank" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            
            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 5px;">Bulan</label>
                <select name="bulan" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-family: inherit;">
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
                    @for($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div style="display: flex; gap: 10px; padding-top: 5px;">
                <button type="submit" onclick="document.getElementById('formStok').action='{{ route('admin.history.report.stok_pdf') }}'; document.getElementById('formStok').target='_blank';" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                    Cetak PDF
                </button>
                <button type="submit" onclick="document.getElementById('formStok').action='{{ route('admin.history.report.stok_excel') }}'; document.getElementById('formStok').target='';" style="background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                    Export Excel
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card" style="margin-bottom: 20px;">
    <div style="background: #fdfdfd; border: 1px solid #eee; border-radius: 12px; padding: 25px;">
        <h4 style="margin-top: 0; font-weight: 700; color: #333; margin-bottom: 15px;">Ekspor Laporan Perbaikan</h4>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">
            Pilih bulan dan tahun untuk menghasilkan laporan Perbaikan. Anda dapat mencetaknya langsung ke PDF atau mengunduh dalam format Excel (CSV).
        </p>
        
        <form id="formPerbaikan" method="GET" action="{{ route('admin.history.report.perbaikan_pdf') }}" target="_blank" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            
            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 5px;">Bulan</label>
                <select name="bulan" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-family: inherit;">
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
                    @for($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div style="display: flex; gap: 10px; padding-top: 5px;">
                <button type="submit" onclick="document.getElementById('formPerbaikan').action='{{ route('admin.history.report.perbaikan_pdf') }}'; document.getElementById('formPerbaikan').target='_blank';" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                    Cetak PDF
                </button>
                <button type="submit" onclick="document.getElementById('formPerbaikan').action='{{ route('admin.history.report.perbaikan_excel') }}'; document.getElementById('formPerbaikan').target='';" style="background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                    Export Excel
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div style="background: #fdfdfd; border: 1px solid #eee; border-radius: 12px; padding: 25px;">
        <h4 style="margin-top: 0; font-weight: 700; color: #333; margin-bottom: 15px;">Ekspor Laporan Gabungan (Semua Kategori)</h4>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">
            Pilih bulan dan tahun untuk menghasilkan laporan gabungan Keluar/Masuk Stok dan Kerusakan Barang. Anda dapat mencetaknya langsung ke PDF atau mengunduh dalam format Excel (CSV).
        </p>
        
        <form id="formCetakLaporan" method="GET" action="{{ route('admin.history.report.pdf') }}" target="_blank" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            
            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 5px;">Bulan</label>
                <select name="bulan" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-family: inherit;">
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
@endsection
