<div class="sidebar-responsive">
    <button class="btn-toggle-sidebar" type="button">
        <i class="bi bi-list"></i>
    </button>
</div>
<div class="sidebar-overlay"></div>
<div class="sidebar">
    <div class="sidebar-header">
        <div class="app-title">
            <h3>SiMonika</h3>
            <small>Sistem Monitoring Aplikasi</small>
        </div>
    </div>

    <nav class="sidebar-nav flex-grow-1">
        <ul class="nav flex-column">
            @if (auth()->user()->role === 'super_admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('super-admin/dashboard') ? 'active' : '' }}"
                            href="{{ route('super-admin.dashboard') }}">
                            <i class="bi bi-house-door"></i>
                            <span>Dashboard Super Admin</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin*') ? 'active' : '' }}"
                            href="{{ route('admin.index') }}">
                            <i class="bi bi-people"></i>
                            <span>Kelola Admin</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}"
                            href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-house-door"></i>
                            <span>Dashboard Admin</span>
                        </a>
                    </li>
                @endif

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('aplikasi.*') ? 'active' : '' }}" href="{{ route('aplikasi.index') }}">
                    <i class="bi bi-grid"></i>
                    <span>Aplikasi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('atribut.*') ? 'active' : '' }}" href="{{ route('atribut.index') }}">
                    <i class="bi bi-clipboard"></i>
                    <span>Atribut</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}">
                    <i class="bi bi-person-vcard"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('linimasa.*') ? 'active' : '' }}" href="{{ route('linimasa.index') }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Linimasa</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('pegawai.*') ? 'active' : '' }}" href="{{ route('pegawai.index') }}">
                    <i class="bi bi-people"></i>
                    <span>Pegawai</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('proyek.*') ? 'active' : '' }}" href="{{ route('proyek.index') }}">
                    <i class="bi bi-briefcase"></i>
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
