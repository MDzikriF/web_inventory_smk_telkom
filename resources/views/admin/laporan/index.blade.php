@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Laporan</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Fitur Laporan Terpisah:</h5>
                        <a href="{{ route('admin.kerusakan.index') }}" class="btn btn-danger">
                            <i class="fas fa-exclamation-triangle"></i> Laporan Kerusakan
                        </a>
                        <a href="{{ route('admin.perbaikan.index') }}" class="btn btn-info">
                            <i class="fas fa-tools"></i> Laporan Perbaikan
                        </a>
                        <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-warning">
                            <i class="fas fa-hand-holding"></i> Laporan Peminjaman
                        </a>
                        <a href="{{ route('admin.laporan_keseluruhan.index') }}" class="btn btn-primary">
                            <i class="fas fa-chart-bar"></i> Laporan Keseluruhan
                        </a>
                    </div>

                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="laporanTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="barang-tab" data-toggle="tab" href="#barang" role="tab">
                                Laporan Barang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="kerusakan-tab" data-toggle="tab" href="#kerusakan" role="tab">
                                Laporan Kerusakan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="perbaikan-tab" data-toggle="tab" href="#perbaikan" role="tab">
                                Laporan Perbaikan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="peminjaman-tab" data-toggle="tab" href="#peminjaman" role="tab">
                                Laporan Peminjaman
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-3" id="laporanTabsContent">
                        <!-- Laporan Barang -->
                        <div class="tab-pane fade show active" id="barang" role="tabpanel">
                            <div class="mb-3">
                                <a href="{{ route('admin.laporan.create_barang') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Laporan Barang
                                </a>
                                <a href="{{ route('admin.laporan.export_barang_masuk_pdf') }}" target="_blank" class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Export Barang Masuk PDF
                                </a>
                                <a href="{{ route('admin.laporan.export_barang_masuk_word') }}" class="btn btn-primary">
                                    <i class="fas fa-file-word"></i> Export Barang Masuk Word
                                </a>
                                <a href="{{ route('admin.laporan.export_barang_masuk_excel') }}" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export Barang Masuk Excel
                                </a>
                                <a href="{{ route('admin.laporan.export_barang_keluar_pdf') }}" target="_blank" class="btn btn-warning">
                                    <i class="fas fa-file-pdf"></i> Export Barang Keluar PDF
                                </a>
                                <a href="{{ route('admin.laporan.export_barang_keluar_word') }}" class="btn btn-primary">
                                    <i class="fas fa-file-word"></i> Export Barang Keluar Word
                                </a>
                                <a href="{{ route('admin.laporan.export_barang_keluar_excel') }}" class="btn btn-info">
                                    <i class="fas fa-file-excel"></i> Export Barang Keluar Excel
                                </a>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Sub Kategori</th>
                                        <th>Type</th>
                                        <th>Jenis</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($laporanBarang as $laporan)
                                    <tr>
                                        <td>{{ $laporan->kode_barang }}</td>
                                        <td>{{ $laporan->nama_barang }}</td>
                                        <td>{{ $laporan->kategori }}</td>
                                        <td>{{ $laporan->sub_kategori }}</td>
                                        <td>{{ $laporan->type }}</td>
                                        <td>{{ $laporan->jenis }}</td>
                                        <td>{{ $laporan->jumlah }}</td>
                                        <td>{{ $laporan->satuan }}</td>
                                        <td>{{ \Carbon\Carbon::parse($laporan->tanggal)->format('d-m-Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Tidak ada data laporan barang</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $laporanBarang->links() }}
                        </div>

                        <!-- Laporan Kerusakan -->
                        <div class="tab-pane fade" id="kerusakan" role="tabpanel">
                            <div class="mb-3">
                                <a href="{{ route('admin.laporan.create_kerusakan') }}" class="btn btn-danger">
                                    <i class="fas fa-plus"></i> Tambah Laporan Kerusakan
                                </a>
                                <a href="{{ route('admin.laporan.export_kerusakan_pdf') }}" target="_blank" class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </a>
                                <a href="{{ route('admin.laporan.export_kerusakan_excel') }}" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </a>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Sub Kategori</th>
                                        <th>Type</th>
                                        <th>Jumlah Rusak</th>
                                        <th>Satuan</th>
                                        <th>Kerusakan</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($laporanKerusakan as $laporan)
                                    <tr>
                                        <td>{{ $laporan->kode_barang }}</td>
                                        <td>{{ $laporan->nama_barang }}</td>
                                        <td>{{ $laporan->kategori }}</td>
                                        <td>{{ $laporan->sub_kategori }}</td>
                                        <td>{{ $laporan->type }}</td>
                                        <td>{{ $laporan->jumlah_rusak }}</td>
                                        <td>{{ $laporan->satuan }}</td>
                                        <td>{{ $laporan->kerusakan }}</td>
                                        <td>
                                            <span class="badge badge-{{ $laporan->status == 'selesai' ? 'success' : ($laporan->status == 'proses' ? 'info' : 'warning') }}">
                                                {{ $laporan->status }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d-m-Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data laporan kerusakan</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $laporanKerusakan->links() }}
                        </div>

                        <!-- Laporan Perbaikan -->
                        <div class="tab-pane fade" id="perbaikan" role="tabpanel">
                            <div class="mb-3">
                                <a href="{{ route('admin.laporan.create_perbaikan') }}" class="btn btn-info">
                                    <i class="fas fa-plus"></i> Tambah Laporan Perbaikan
                                </a>
                                <a href="{{ route('admin.laporan.export_perbaikan_pdf') }}" target="_blank" class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </a>
                                <a href="{{ route('admin.laporan.export_perbaikan_word') }}" class="btn btn-primary">
                                    <i class="fas fa-file-word"></i> Export Word
                                </a>
                                <a href="{{ route('admin.laporan.export_perbaikan_excel') }}" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </a>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Sub Kategori</th>
                                        <th>Type</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($laporanPerbaikan as $laporan)
                                    <tr>
                                        <td>{{ $laporan->kode_barang }}</td>
                                        <td>{{ $laporan->nama_barang }}</td>
                                        <td>{{ $laporan->kategori }}</td>
                                        <td>{{ $laporan->sub_kategori }}</td>
                                        <td>{{ $laporan->type }}</td>
                                        <td>{{ $laporan->jumlah_rusak }}</td>
                                        <td>{{ $laporan->satuan }}</td>
                                        <td>{{ $laporan->kerusakan }}</td>
                                        <td>
                                            <span class="badge badge-{{ $laporan->status == 'selesai' ? 'success' : ($laporan->status == 'proses' ? 'info' : 'warning') }}">
                                                {{ $laporan->status }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d-m-Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data laporan perbaikan</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $laporanPerbaikan->links() }}
                        </div>

                        <!-- Laporan Peminjaman -->
                        <div class="tab-pane fade" id="peminjaman" role="tabpanel">
                            <div class="mb-3">
                                <a href="{{ route('admin.laporan.create_peminjaman') }}" class="btn btn-warning">
                                    <i class="fas fa-plus"></i> Tambah Laporan Peminjaman
                                </a>
                                <a href="{{ route('admin.laporan.export_peminjaman_pdf') }}" target="_blank" class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </a>
                                <a href="{{ route('admin.laporan.export_peminjaman_word') }}" class="btn btn-primary">
                                    <i class="fas fa-file-word"></i> Export Word
                                </a>
                                <a href="{{ route('admin.laporan.export_peminjaman_excel') }}" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </a>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Sub Kategori</th>
                                        <th>Type</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($laporanPeminjaman as $laporan)
                                    <tr>
                                        <td>{{ $laporan->kode_barang }}</td>
                                        <td>{{ $laporan->nama_barang }}</td>
                                        <td>{{ $laporan->kategori }}</td>
                                        <td>{{ $laporan->sub_kategori }}</td>
                                        <td>{{ $laporan->type }}</td>
                                        <td>{{ $laporan->jumlah_rusak }}</td>
                                        <td>{{ $laporan->satuan }}</td>
                                        <td>{{ $laporan->kerusakan }}</td>
                                        <td>
                                            <span class="badge badge-{{ $laporan->status == 'selesai' ? 'success' : ($laporan->status == 'proses' ? 'info' : 'warning') }}">
                                                {{ $laporan->status }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d-m-Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data laporan peminjaman</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $laporanPeminjaman->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection