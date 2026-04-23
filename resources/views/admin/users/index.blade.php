@extends('layouts.admin')
@section('title', 'Data Pengguna')

@section('content')
<h1 class="page-title">Data Pengguna</h1>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-weight: 600; color: var(--text-dark);">Daftar Pengguna Sistem</h3>
        <button class="btn btn-primary" onclick="openModal('addModal')" style="display:flex; align-items:center; gap:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Baru
        </button>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Profil Pengguna</th>
                    <th>Hak Akses (Role)</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-dark);">{{ $user->name }}</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">
                            <strong>NIP/NIS:</strong> {{ $user->nip }} &bull; {{ $user->email ?: '-' }}
                        </div>
                    </td>
                    <td>
                        <span style="background: {{ $user->role === 'admin' ? 'rgba(231, 76, 60, 0.1)' : 'rgba(52, 152, 219, 0.1)' }}; color: {{ $user->role === 'admin' ? '#c0392b' : '#2980b9' }}; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span style="display:inline-block; padding: 4px 8px; border-radius: 4px; background: rgba(39, 174, 96, 0.1); color: #27ae60; font-size: 0.8rem; font-weight:600;">Aktif</span>
                        @else
                            <span style="display:inline-block; padding: 4px 8px; border-radius: 4px; background: rgba(231, 76, 60, 0.1); color: #c0392b; font-size: 0.8rem; font-weight:600;">Non-aktif</span>
                        @endif
                    </td>
                    <td style="color: var(--text-muted);">{{ $user->created_at->format('d M Y') }}</td>
                    <td style="text-align:center;">
                        <div style="display:flex; gap:10px; justify-content:center;">
                            <!-- Edit Button -->
                            <button class="action-btn" title="Edit" onclick="openModal('editModal{{$user->nip}}')">
                                ✏️
                            </button>
                            
                            <!-- Delete Button -->
                            @if(Auth::id() !== $user->nip)
                            <form action="{{ route('admin.users.destroy', $user->nip) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>

                @push('modals')
                <!-- Edit Modal for this User -->
                <div class="modal-overlay" id="editModal{{$user->nip}}">
                    <div class="modal">
                        <div class="modal-header">
                            <h3 style="font-weight: 700; color: var(--text-dark);">Edit Pengguna</h3>
                            <button class="close-modal" onclick="closeModal('editModal{{$user->nip}}')">&times;</button>
                        </div>
                        <form action="{{ route('admin.users.update', $user->nip) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">NIP</label>
                                <input type="text" name="nip" class="form-control" value="{{ $user->nip }}" readonly style="background: #f1f2f6; cursor: not-allowed;" title="NIP tidak dapat diubah">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Keterangan (Jurusan / Jabatan)</label>
                                <input type="text" name="email" class="form-control" value="{{ $user->email }}" placeholder="Cth: Siswa RPL / Guru IT">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Hak Akses</label>
                                <select name="role" class="form-control" required style="background:white;">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User (Mahasiswa/Lab)</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin Inventaris</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status Akun</label>
                                <select name="is_active" class="form-control" required style="background:white;">
                                    <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Non-aktif (Blokir)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ganti Kata Sandi (Kosongkan jika tidak ingin diubah)</label>
                                <input type="password" name="password" class="form-control" placeholder="Kata sandi baru">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
                @endpush

                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('modals')
<!-- Add User Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div class="modal-header">
            <h3 style="font-weight: 700; color: var(--text-dark);">Tambah Pengguna Baru</h3>
            <button class="close-modal" onclick="closeModal('addModal')">&times;</button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required placeholder="Masukkan nama">
            </div>
            <div class="form-group">
                <label class="form-label">NIP</label>
                <input type="text" name="nip" class="form-control" required placeholder="Masukkan NIP">
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan (Jurusan / Jabatan)</label>
                <input type="text" name="email" class="form-control" placeholder="Cth: Siswa RPL / Guru IT">
            </div>
            <div class="form-group">
                <label class="form-label">Hak Akses</label>
                <select name="role" class="form-control" required style="background:white;">
                    <option value="user">User (Mahasiswa/Lab)</option>
                    <option value="admin">Admin Inventaris</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Kata Sandi (Default)</label>
                <input type="text" name="password" class="form-control" required value="password" readonly style="background: #f1f2f6; cursor: not-allowed;">
                <small style="color:var(--text-muted); margin-top:5px; display:block;">Password awal adalah 'password' dan dapat diubah nanti.</small>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Tambah Akun</button>
        </form>
    </div>
</div>
@endpush

@endsection
