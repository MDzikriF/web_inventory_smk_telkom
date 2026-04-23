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
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="laporanTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="barang-tab" data-toggle="tab" href="#barang" role="tab">
                                Laporan Barang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="rusak-tab" data-toggle="tab" href="#rusak" role="tab">
                                Laporan Rusak
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

                        <!-- Laporan Rusak -->
                        <div class="tab-pane fade" id="rusak" role="tabpanel">
                            <div class="mb-3">
                                <a href="{{ route('admin.laporan.create_rusak') }}" class="btn btn-danger">
                                    <i class="fas fa-plus"></i> Tambah Laporan Rusak
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
                                    @forelse($laporanRusak as $laporan)
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
                                            <span class="badge badge-{{ $laporan->status == 'resolved' ? 'success' : 'warning' }}">
                                                {{ $laporan->status }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d-m-Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data laporan rusak</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $laporanRusak->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection