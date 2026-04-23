<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Inventory Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .admin-topbar { justify-content: center; position: relative; }
        .admin-topbar .page-trigger { position: absolute; left: 30px; }
        .admin-topbar .admin-title { position: absolute; left: 80px; top: 50%; transform: translateY(-50%); font-size: 1rem; font-weight: 800; color: var(--text-dark); letter-spacing: 2px; margin: 0; }
        .admin-topbar .center-logo { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); }
        .admin-topbar .center-logo img { max-height: 60px; transition: all 0.3s ease; }
        .admin-topbar .logout-section { position: absolute; right: 30px; }
        @media (max-width: 768px) {
            .admin-topbar .center-logo { display: none; }
            .admin-topbar .admin-title { left: 60px; font-size: 0.9rem; }
            .admin-topbar .page-trigger { left: 20px; }
            .admin-topbar .logout-section { right: 15px; }
        }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border-bottom: 1px solid var(--border-color); padding: 15px; text-align: left; }
        th { color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; }
        .action-btn { background: none; border: none; cursor: pointer; color: var(--primary); font-size: 1.1rem; padding: 5px; transition: transform 0.2s; }
        .action-btn:hover { transform: scale(1.1); }
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; }
        .modal {
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-height: 90vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        @media (max-width: 600px) {
            .modal {
                max-width: 98vw;
                padding: 12px;
            }
        }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;}
        .close-modal { background: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); border: none; }
        .menu-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 22px; background: #dc3545; color: white; border-radius: 999px; font-size: 0.7rem; font-weight: 700; padding: 0 8px; margin-left: 8px; }
    </style>
</head>
<body>
    @php
        $adminUnreadCount = auth()->check() ? auth()->user()->notifications()->where('is_read', false)->count() : 0;
        $pendingRequests = auth()->check() ? \App\Models\ItemRequest::whereIn('status', ['pending', 'return_requested'])->count() : 0;
        $pendingDamages = auth()->check() ? \App\Models\DamageReport::where('status', 'pending')->count() : 0;
        $pendingValidationCount = $pendingRequests + $pendingDamages;
        $pendingUserCount = auth()->check() ? \App\Models\User::where('is_active', false)->count() : 0;
        $adminNips = auth()->check() ? \App\Models\User::where('role', 'admin')->pluck('nip')->toArray() : [];
        $unreadChatCount = auth()->check() ? \App\Models\Message::whereIn('receiver_id', $adminNips)->where('is_read', false)->count() : 0;
    @endphp
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header" style="justify-content: center;">
                <div style="font-size: 1.5rem; color: #5d5454; font-family: 'Fredoka', 'Inter', sans-serif;">Lab</div>
                <button class="page-trigger" onclick="toggleSidebar()" id="closeSidebar" style="display:none; position:absolute; right:20px;">&times;</button>
            </div>
            
            <div class="sidebar-menu">
                <a href="{{ route('admin.users.index') }}" class="menu-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                    <span class="menu-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></span> Data Pengguna
                    @if($pendingUserCount > 0)
                        <span class="menu-badge" style="background:#f39c12;">{{ $pendingUserCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.items.index') }}" class="menu-item {{ request()->is('admin/items*') ? 'active' : '' }}">
                    <span class="menu-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg></span> Kelola Barang
                </a>

                <a href="{{ route('admin.validations.index') }}" class="menu-item {{ request()->is('admin/validations*') ? 'active' : '' }}">
                    <span class="menu-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></span> Validasi
                    @if($pendingValidationCount > 0)
                        <span class="menu-badge" style="background:#e67e22;">{{ $pendingValidationCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.chat.index') }}" class="menu-item {{ request()->is('admin/chat*') ? 'active' : '' }}">
                    <span class="menu-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></span> Chat
                    @if($unreadChatCount > 0)
                        <span class="menu-badge" style="background:#2ecc71;">{{ $unreadChatCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.settings.index') }}" class="menu-item {{ request()->is('admin/settings*') ? 'active' : '' }}">
                    <span class="menu-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M12 1v6m0 6v6m4.22-13.22l4.24 4.24M1.54 1.54l4.24 4.24M20.46 20.46l-4.24-4.24M1.54 20.46l4.24-4.24"></path></svg></span> Pengaturan
                </a>
                <a href="{{ route('admin.notifications.index') }}" class="menu-item {{ request()->is('admin/notifications*') ? 'active' : '' }}">
                    <span class="menu-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></span> Histori & Notifikasi
                    @if($adminUnreadCount > 0)
                        <span id="adminNotificationBadge" class="menu-badge">{{ $adminUnreadCount }}</span>
                    @endif
                </a>

            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar admin-topbar">
                <button class="page-trigger" onclick="toggleSidebar()">☰</button>
                <h2 class="admin-title">ADMIN</h2>
                <div class="center-logo">
                    <img src="{{ asset('images/logosmktelkom.png') }}" alt="SMK Telkom Jakarta">
                </div>
                
                <div class="logout-section">
                    <button class="btn" style="background:transparent; color: var(--primary);" onclick="openModal('logoutConfirmModal')">Logout</button>
                </div>
            </header>

            <div class="content-area">
                @if(session('success'))
                    <div style="background: rgba(46, 204, 113, 0.2); color: #27ae60; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div style="background: rgba(231, 76, 60, 0.2); color: #c0392b; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('modals')

    <!-- Logout Confirm Modal -->
    <div class="modal-overlay" id="logoutConfirmModal">
        <div class="modal" style="max-width: 400px; text-align: center;">
            <div class="modal-header" style="justify-content: center; border-bottom: none; padding-bottom: 0;">
                <h3 style="font-weight: 700; color: #e52e2e; margin: 0;">Konfirmasi Keluar</h3>
                <button class="close-modal" onclick="closeModal('logoutConfirmModal')" style="position: absolute; right: 25px; top: 25px;">&times;</button>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin-top: 20px;">
                @csrf
                <p style="margin-bottom: 20px; color: var(--text-muted); font-size: 0.95rem;">Silakan masukkan kata sandi Anda untuk memverifikasi dan keluar dari panel admin.</p>
                <div style="text-align: left; margin-bottom: 20px;">
                    <input type="password" name="password" required placeholder="Masukkan kata sandi..." style="width: 100%; border: 1px solid #c4bfb7; border-radius: 8px; padding: 12px; font-family: inherit; font-size: 1rem;">
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closeModal('logoutConfirmModal')" style="flex: 1; padding: 14px; background: #f1f2f6; color: var(--text-dark); border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: background 0.2s;">Batal</button>
                    <button type="submit" style="flex: 1; padding: 14px; background: #e52e2e; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: background 0.2s;">Keluar Sistem</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const closeBtn = document.getElementById('closeSidebar');
            sidebar.classList.toggle('show');
            if (window.innerWidth <= 768) {
                closeBtn.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
            }
        }

        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
    </script>

<!-- Footer -->
<footer style="background: var(--primary-hover); color: white; text-align: center; padding: 20px; margin-top: auto; border-top: 3px solid rgba(0,0,0,0.1);">
    <div style="max-width: 1200px; margin: 0 auto;">
        <p style="margin: 0; font-size: 0.9rem; font-weight: 500;">
            Mahasiswa Teknik Informatika 2023
        </p>
        <p style="margin: 8px 0 0 0; font-size: 0.85rem; opacity: 0.9;">
            Universitas Mercu Buana
        </p>
        <div style="margin-top: 10px; font-size: 0.75rem; opacity: 0.7;">
            © 2026 Sistem Inventaris Lab
        </div>
    </div>
</footer>

</body>
</html>
