@extends('layouts.admin')

@section('title', 'Laporan Keseluruhan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Laporan Keseluruhan</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <a href="{{ route('admin.laporan_keseluruhan.export_pdf') }}" target="_blank" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('admin.laporan_keseluruhan.export_excel') }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export Excel
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
                                    @forelse($kerusakan as $item)
                                    <tr>
                                        <td>{{ $item->kode_barang }}</td>
                                        <td>{{ $item->nama_barang }}</td>
                                        <td>{{ $item->kategori }}</td>
                                        <td>{{ $item->sub_kategori }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>{{ $item->jumlah_rusak }}</td>
                                        <td>{{ $item->satuan }}</td>
                                        <td>{{ $item->kerusakan }}</td>
                                        <td>
                                            <span class="badge badge-{{ $item->status == 'selesai' ? 'success' : ($item->status == 'proses' ? 'info' : 'warning') }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_lapor)->format('d-m-Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data laporan kerusakan</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $kerusakan->links() }}
                        </div>

                        <!-- Laporan Perbaikan -->
                        <div class="tab-pane fade" id="perbaikan" role="tabpanel">
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
                                        <th>Deskripsi Perbaikan</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($perbaikan as $item)
                                    <tr>
                                        <td>{{ $item->kode_barang }}</td>
                                        <td>{{ $item->nama_barang }}</td>
                                        <td>{{ $item->kategori }}</td>
                                        <td>{{ $item->sub_kategori }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>{{ $item->jumlah_diperbaiki }}</td>
                                        <td>{{ $item->satuan }}</td>
                                        <td>{{ $item->deskripsi_perbaikan }}</td>
                                        <td>
                                            <span class="badge badge-{{ $item->status == 'selesai' ? 'success' : ($item->status == 'proses' ? 'info' : 'warning') }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_perbaikan)->format('d-m-Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data laporan perbaikan</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $perbaikan->links() }}
                        </div>

                        <!-- Laporan Peminjaman -->
                        <div class="tab-pane fade" id="peminjaman" role="tabpanel">
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
                                        <th>Peminjam</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($peminjaman as $item)
                                    <tr>
                                        <td>{{ $item->kode_barang }}</td>
                                        <td>{{ $item->nama_barang }}</td>
                                        <td>{{ $item->kategori }}</td>
                                        <td>{{ $item->sub_kategori }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>{{ $item->jumlah_dipinjam }}</td>
                                        <td>{{ $item->satuan }}</td>
                                        <td>{{ $item->peminjam }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_peminjaman)->format('d-m-Y') }}</td>
                                        <td>{{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y') : '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $item->status == 'dikembalikan' ? 'success' : ($item->status == 'terlambat' ? 'danger' : 'warning') }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="11" class="text-center">Tidak ada data laporan peminjaman</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $peminjaman->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
