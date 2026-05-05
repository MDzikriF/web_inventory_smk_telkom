@extends('layouts.admin')

@section('title', 'Laporan Perbaikan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Laporan Perbaikan</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <a href="{{ route('admin.perbaikan.create') }}" class="btn btn-info">
                            <i class="fas fa-plus"></i> Tambah Laporan Perbaikan
                        </a>
                        <a href="{{ route('admin.perbaikan.export_pdf') }}" target="_blank" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('admin.perbaikan.export_word') }}" class="btn btn-primary">
                            <i class="fas fa-file-word"></i> Export Word
                        </a>
                        <a href="{{ route('admin.perbaikan.export_excel') }}" class="btn btn-success">
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
                                <th>Deskripsi Perbaikan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
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
                                <td>
                                    <a href="{{ route('admin.perbaikan.edit', $item->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('admin.perbaikan.destroy', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center">Tidak ada data laporan perbaikan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $perbaikan->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
