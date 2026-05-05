@extends('layouts.admin')

@section('title', 'Laporan Kerusakan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Laporan Kerusakan</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <a href="{{ route('admin.kerusakan.create') }}" class="btn btn-danger">
                            <i class="fas fa-plus"></i> Tambah Laporan Kerusakan
                        </a>
                        <a href="{{ route('admin.kerusakan.export_pdf') }}" target="_blank" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('admin.kerusakan.export_excel') }}" class="btn btn-success">
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
                                <th>Aksi</th>
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
                                <td>
                                    <a href="{{ route('admin.kerusakan.edit', $item->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('admin.kerusakan.destroy', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center">Tidak ada data laporan kerusakan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $kerusakan->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
