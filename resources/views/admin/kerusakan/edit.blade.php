@extends('layouts.admin')

@section('title', 'Edit Laporan Kerusakan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Laporan Kerusakan</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.kerusakan.update', $kerusakan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_barang">Kode Barang</label>
                                    <input type="text" class="form-control" id="kode_barang" name="kode_barang" value="{{ $kerusakan->kode_barang }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_barang">Nama Barang</label>
                                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="{{ $kerusakan->nama_barang }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kategori">Kategori</label>
                                    <input type="text" class="form-control" id="kategori" name="kategori" value="{{ $kerusakan->kategori }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sub_kategori">Sub Kategori</label>
                                    <input type="text" class="form-control" id="sub_kategori" name="sub_kategori" value="{{ $kerusakan->sub_kategori }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <input type="text" class="form-control" id="type" name="type" value="{{ $kerusakan->type }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jumlah_rusak">Jumlah Rusak</label>
                                    <input type="number" class="form-control" id="jumlah_rusak" name="jumlah_rusak" value="{{ $kerusakan->jumlah_rusak }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="satuan">Satuan</label>
                                    <input type="text" class="form-control" id="satuan" name="satuan" value="{{ $kerusakan->satuan }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_lapor">Tanggal Lapor</label>
                                    <input type="date" class="form-control" id="tanggal_lapor" name="tanggal_lapor" value="{{ $kerusakan->tanggal_lapor->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="kerusakan">Kerusakan</label>
                            <textarea class="form-control" id="kerusakan" name="kerusakan" rows="3" required>{{ $kerusakan->kerusakan }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ $kerusakan->keterangan }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending" {{ $kerusakan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="proses" {{ $kerusakan->status == 'proses' ? 'selected' : '' }}>Proses</option>
                                <option value="selesai" {{ $kerusakan->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-danger">Update</button>
                            <a href="{{ route('admin.kerusakan.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
