@extends('layouts.user')
@section('title', 'Profil Saya')

@section('content')
<h1 class="page-title">Pengaturan Akun</h1>

<div class="card" style="max-width: 600px;">
    <div style="display:flex; align-items:center; gap:20px; margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
        <div style="width: 70px; height: 70px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h3 style="font-weight: 700; color: var(--text-dark);">{{ $user->name }}</h3>
            <p style="color: var(--text-muted); margin-bottom: {{ $user->email ? '4px' : '0' }};">{{ $user->nip }} (NIP/NIM)</p>
            @if($user->email)
               <p style="color: var(--text-muted); font-size: 0.95rem; display: flex; align-items: center; gap: 6px; font-weight: 500; margin: 0 0 5px 0;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                    {{ $user->email }}
               </p>
            @endif
            @if($user->is_active)
                <span style="display:inline-block; padding: 4px 8px; border-radius: 4px; background: rgba(39, 174, 96, 0.1); color: #27ae60; font-size: 0.8rem; font-weight:600; margin-top:5px;">Akun Aktif</span>
            @else
                <span style="display:inline-block; padding: 4px 8px; border-radius: 4px; background: rgba(231, 76, 60, 0.1); color: #c0392b; font-size: 0.8rem; font-weight:600; margin-top:5px;">Akun Non-aktif</span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 15px; background: rgba(39, 174, 96, 0.1); color: #27ae60; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="padding: 15px; background: rgba(231, 76, 60, 0.1); color: #e74c3c; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin:0; padding-left:20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
        </div>
        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
