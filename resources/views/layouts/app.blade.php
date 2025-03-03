<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI MONIKA - Sistem Informasi Monitoring Kinerja</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css"
        rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <!-- Sidebar Responsive Button -->
    <div class="sidebar-responsive">
        <button class="btn-toggle-sidebar" type="button">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="app-title">
                <h3>SiMonika</h3>
                <small>Sistem Monitoring Aplikasi</small>
            </div>
        </div>

        <nav class="sidebar-nav flex-grow-1">
            <ul class="nav flex-column">
                <!-- Dashboard Utama (Semua User Bisa Akses) -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}"
                        href="{{ url('/dashboard') }}">
                        <i class="bi bi-house-door"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Dashboard Admin (Hanya untuk Admin & Super Admin) -->
                @if (auth()->user()->role === 'super_admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}"
                            href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard Admin</span>
                        </a>
                    </li>
                @endif

                <!-- Kelola Admin (Hanya Super Admin) -->
                @if (auth()->user()->role === 'super_admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin*') ? 'active' : '' }}"
                            href="{{ route('admin.index') }}">
                            <i class="bi bi-people"></i>
                            <span>Kelola Admin</span>
                        </a>
                    </li>
                @endif

                <!-- Menu yang Bisa Diakses Semua User -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('aplikasi*') ? 'active' : '' }}"
                        href="{{ route('aplikasi.index') }}">
                        <i class="bi bi-grid"></i>
                        <span>Aplikasi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('atribut*') ? 'active' : '' }}"
                        href="{{ route('atribut.index') }}">
                        <i class="bi bi-clipboard"></i>
                        <span>Atribut</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('profile*') ? 'active' : '' }}"
                        href="{{ route('profile.index') }}">
                        <i class="bi bi-person-vcard"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('linimasa*') ? 'active' : '' }}"
                        href="{{ route('linimasa.index') }}">
                        <i class="bi bi-calendar-event"></i>
                        <span>Linimasa Proyek</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pegawai*') ? 'active' : '' }}"
                        href="{{ route('pegawai.index') }}">
                        <i class="bi bi-people"></i>
                        <span>Pendaftaran Pegawai</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('proyek*') ? 'active' : '' }}"
                        href="{{ route('proyek.index') }}">
                        <i class="bi bi-people"></i>
                        <span>Proyek</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="button nav-link">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>

</html>
