@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="dashboard-container">
    <h1>Dashboard Admin</h1>
    <p>Selamat datang di panel admin Sistem Inventaris Lab.</p>

    @if($lowStockItems->count() > 0)
        <div class="alert alert-warning">
            <h4>Notifikasi Stok Minimum</h4>
            <p>Item berikut memiliki stok di bawah minimum:</p>
            <ul>
                @foreach($lowStockItems as $item)
                    <li>{{ $item->name }} - Stok: {{ $item->stock }}, Minimum: {{ $item->min_stock }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tambahkan konten dashboard admin di sini -->
</div>
@endsection