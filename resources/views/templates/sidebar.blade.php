<div class="sidebar-responsive">
    <button class="btn-toggle-sidebar" type="button" aria-label="Buka navigasi">
        <i class="bi bi-list"></i>
    </button>
</div>
<div class="sidebar-overlay"></div>
<aside class="sidebar">
    <div class="sidebar-header px-3 pt-3">
        <div class="app-title text-start">
            <h3 class="mb-0">SiMonika</h3>
            <small>Monitoring aplikasi dan pekerjaan</small>
        </div>
    </div>

    <nav class="sidebar-nav flex-grow-1 overflow-auto" aria-label="Navigasi utama">
        <ul class="nav flex-column">
            @if (auth()->user()->isSuperAdmin())
                <li class="nav-section">Tata kelola</li>
                <li>
                    <a
                        class="nav-link {{ request()->routeIs('super-admin.*') ? 'active' : '' }}"
                        href="{{ route('super-admin.dashboard') }}"
                        ><i class="bi bi-shield-check"></i><span>Audit sistem</span></a
                    >
                </li>
                <li>
                    <a
                        class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                        href="{{ route('admin.index') }}"
                        ><i class="bi bi-person-gear"></i><span>Kelola admin</span></a
                    >
                </li>
            @endif

            <li class="nav-section">Inventaris</li>
            <li>
                <a
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}"
                    ><i class="bi bi-grid-1x2"></i><span>Ringkasan</span></a
                >
            </li>
            <li>
                <a
                    class="nav-link {{ request()->routeIs('aplikasi.*') ? 'active' : '' }}"
                    href="{{ route('aplikasi.index') }}"
                    ><i class="bi bi-window-stack"></i><span>Aplikasi</span></a
                >
            </li>
            <li>
                <a
                    class="nav-link {{ request()->routeIs('atribut.*') ? 'active' : '' }}"
                    href="{{ route('atribut.index') }}"
                    ><i class="bi bi-sliders"></i><span>Atribut</span></a
                >
            </li>

            <li class="nav-section">Operasional</li>
            <li>
                <a
                    class="nav-link {{ request()->routeIs('linimasa.*') ? 'active' : '' }}"
                    href="{{ route('linimasa.index') }}"
                    ><i class="bi bi-calendar3"></i><span>Linimasa</span></a
                >
            </li>
            <li>
                <a
                    class="nav-link {{ request()->routeIs('pegawai.*') ? 'active' : '' }}"
                    href="{{ route('pegawai.index') }}"
                    ><i class="bi bi-people"></i><span>Pegawai</span></a
                >
            </li>
            <li>
                <a
                    class="nav-link {{ request()->routeIs('proyek.*') || request()->routeIs('kategori.*') ? 'active' : '' }}"
                    href="{{ route('proyek.index') }}"
                    ><i class="bi bi-kanban"></i><span>Proyek</span></a
                >
            </li>
            <li>
                <a
                    class="nav-link {{ request()->routeIs('pendataan.*') ? 'active' : '' }}"
                    href="{{ route('pendataan.index') }}"
                    ><i class="bi bi-mortarboard"></i><span>Program magang</span></a
                >
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer p-3">
        <a class="account-link" href="{{ route('profile.index') }}">
            <i class="bi bi-person-circle"></i>
            <span
                ><strong>{{ auth()->user()->nama }}</strong
                ><small>{{ str_replace('_', ' ', auth()->user()->role) }}</small></span
            >
        </a>
        <form action="{{ route('logout') }}" method="POST" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm w-100">
                <i class="bi bi-box-arrow-right me-1"></i>Keluar
            </button>
        </form>
    </div>
</aside>
