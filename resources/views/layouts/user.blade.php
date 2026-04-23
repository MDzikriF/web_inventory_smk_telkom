<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Inventory Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .topbar { position: relative; }
        .topbar .center-logo { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); }
        .topbar .center-logo img { max-height: 42px; transition: all 0.3s ease; }
        @media (max-width: 768px) {
            .topbar .center-logo { display: none; }
        }
    </style>
</head>
<body>
    @php
        $userUnreadCount = auth()->check() ? auth()->user()->notifications()->where('is_read', false)->count() : 0;
        $unreadChatCount = auth()->check() ? \App\Models\Message::where('receiver_id', auth()->user()->nip)->where('is_read', false)->count() : 0;
        $unreadPengaduanCount = auth()->check() ? auth()->user()->notifications()->where('is_read', false)->where('title', 'like', '%Kerusakan%')->count() : 0;
    @endphp
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div style="display:flex; align-items:center; gap:10px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    Inv-Lab
                </div>
                <button class="page-trigger" onclick="toggleSidebar()" id="closeSidebar" style="display:none;">&times;</button>
            </div>
            
            <div class="sidebar-menu">
                <div style="padding: 10px 20px; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Menu Akses</div>
                
                <a href="{{ route('user.dashboard') }}" class="menu-item {{ request()->is('user/dashboard*') ? 'active' : '' }}">
                    <span class="menu-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="12" y1="20" x2="12" y2="10"></line><line x1="18" y1="20" x2="18" y2="4"></line><line x1="6" y1="20" x2="6" y2="16"></line></svg></span> Dashboard
                </a>
                <a href="{{ route('user.catalog.index') }}" class="menu-item {{ request()->is('user/catalog*') ? 'active' : '' }}">
                    <span class="menu-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg></span> Katalog Barang
                </a>
                <a href="{{ route('user.complaints.index') }}" class="menu-item {{ request()->is('user/complaints*') ? 'active' : '' }}">
                    <span class="menu-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></span> Pengaduan
                    @if($unreadPengaduanCount > 0)
                        <span class="menu-badge" style="background:#e67e22;">{{ $unreadPengaduanCount }}</span>
                    @endif
                </a>
                <a href="{{ route('user.notifications.index') }}" class="menu-item {{ request()->is('user/notifications*') ? 'active' : '' }}">
                    <span class="menu-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg></span> Notifikasi
                    @if($userUnreadCount > 0)
                        <span class="menu-badge" style="background:#e74c3c;">{{ $userUnreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('user.chat.index') }}" class="menu-item {{ request()->is('user/chat*') ? 'active' : '' }}">
                    <span class="menu-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></span> Chat Admin
                    @if($unreadChatCount > 0)
                        <span class="menu-badge" style="background:#2ecc71;">{{ $unreadChatCount }}</span>
                    @endif
                </a>
            </div>
            
            <div style="padding: 20px; border-top: 1px solid var(--border-color);">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-block" style="background: rgba(201, 26, 37, 0.1); color: var(--primary);"><svg width="18" height="18" style="margin-right: 5px; display: inline-block; vertical-align: text-bottom;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> Logout</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <div class="center-logo">
                    <img src="{{ asset('images/logosmktelkom.png') }}" alt="SMK Telkom Jakarta">
                </div>
                <div style="display: flex; align-items:center;">
                    <button class="page-trigger" onclick="toggleSidebar()" style="margin-right: 15px;">☰</button>
                    <h2 style="font-size: 1.1rem; font-weight: 600; color: var(--text-dark);">@yield('title')</h2>
                </div>
                
                <div style="display: flex; align-items: center; gap: 15px; position:relative;">
                    <div onclick="toggleProfileMenu()" style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; cursor:pointer;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div style="display: none; flex-direction: column;" class="d-md-flex">
                        <span style="font-weight: 600; font-size: 0.95rem;">{{ Auth::user()->name ?? 'User Pengguna' }}</span>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">Mahasiswa / Lab</span>
                    </div>

                    <!-- Profile Dropdown Menu -->
                    <div id="profileDropdown" style="display:none; position:absolute; top:50px; right:0; background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.1); width:150px; overflow:hidden; z-index:100;">
                        <a href="{{ route('user.profile.index') }}" style="display:flex; align-items:center; padding:10px 15px; color:#333; text-decoration:none; font-size:0.9rem; border-bottom:1px solid #eee;"><svg width="16" height="16" style="margin-right: 8px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Profil Saya</a>
                        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" style="width:100%; text-align:left; padding:10px 15px; background:none; border:none; color:#e74c3c; font-size:0.9rem; cursor:pointer; display:flex; align-items:center;"><svg width="16" height="16" style="margin-right: 8px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> Keluar</button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Responsive Sidebar Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const closeBtn = document.getElementById('closeSidebar');
            sidebar.classList.toggle('show');
            if (window.innerWidth <= 768) {
                closeBtn.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
            }
        }
        
        function toggleProfileMenu() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }
        
        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            if (!document.getElementById('profileDropdown').contains(e.target) && !e.target.closest('[onclick="toggleProfileMenu()"]')) {
                document.getElementById('profileDropdown').style.display = 'none';
            }
        });
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
