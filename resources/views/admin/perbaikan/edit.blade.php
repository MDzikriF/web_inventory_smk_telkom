@extends('layouts.admin')

@section('title', 'Edit Laporan Perbaikan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Laporan Perbaikan</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.perbaikan.update', $perbaikan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_barang">Kode Barang</label>
                                    <input type="text" class="form-control" id="kode_barang" name="kode_barang" value="{{ $perbaikan->kode_barang }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_barang">Nama Barang</label>
                                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="{{ $perbaikan->nama_barang }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kategori">Kategori</label>
                                    <input type="text" class="form-control" id="kategori" name="kategori" value="{{ $perbaikan->kategori }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sub_kategori">Sub Kategori</label>
                                    <input type="text" class="form-control" id="sub_kategori" name="sub_kategori" value="{{ $perbaikan->sub_kategori }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <input type="text" class="form-control" id="type" name="type" value="{{ $perbaikan->type }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jumlah_diperbaiki">Jumlah Diperbaiki</label>
                                    <input type="number" class="form-control" id="jumlah_diperbaiki" name="jumlah_diperbaiki" value="{{ $perbaikan->jumlah_diperbaiki }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="satuan">Satuan</label>
                                    <input type="text" class="form-control" id="satuan" name="satuan" value="{{ $perbaikan->satuan }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_perbaikan">Tanggal Perbaikan</label>
                                    <input type="date" class="form-control" id="tanggal_perbaikan" name="tanggal_perbaikan" value="{{ $perbaikan->tanggal_perbaikan->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi_perbaikan">Deskripsi Perbaikan</label>
                            <textarea class="form-control" id="deskripsi_perbaikan" name="deskripsi_perbaikan" rows="3" required>{{ $perbaikan->deskripsi_perbaikan }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ $perbaikan->keterangan }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending" {{ $perbaikan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="proses" {{ $perbaikan->status == 'proses' ? 'selected' : '' }}>Proses</option>
                                <option value="selesai" {{ $perbaikan->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-info">Update</button>
                            <a href="{{ route('admin.perbaikan.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
