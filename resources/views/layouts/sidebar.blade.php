<!-- resources/views/layouts/sidebar.blade.php -->
<div class="flex h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Sidebar -->
    <aside
        class="w-64 text-white flex flex-col bg-gradient-to-b from-emerald-600 via-green-700 to-green-900 shadow-lg">
        
        <!-- Logo -->
        <div class="h-16 flex items-center justify-center border-b border-green-500/40">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <x-application-logo class="block h-8 w-auto text-white" />
                <span class="font-bold text-lg tracking-wide">SIAJM</span>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition 
                {{ request()->routeIs('dashboard') ? 'bg-white/20 font-semibold shadow-inner' : 'hover:bg-white/10' }}">
                <i class="fi fi-rr-home"></i>
                <span>Dashboard</span>
            </a>

            @can('role-A')
            <a href="{{ route('karyawans.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition 
                {{ request()->routeIs('karyawans.index') ? 'bg-white/20 font-semibold shadow-inner' : 'hover:bg-white/10' }}">
                <i class="fi fi-rr-people"></i>
                <span>Data Karyawan</span>
            </a>
            <a href="{{ route('departemen.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition 
                {{ request()->routeIs('departemen.index') ? 'bg-white/20 font-semibold shadow-inner' : 'hover:bg-white/10' }}">
                <i class="fi fi-rr-building"></i>
                <span>Departemen</span>
            </a>
            <a href="{{ route('kategori.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition 
                {{ request()->routeIs('kategori.index') ? 'bg-white/20 font-semibold shadow-inner' : 'hover:bg-white/10' }}">
                <i class="fi fi-rr-layers"></i>
                <span>Kategori</span>
            </a>
            <a href="{{ route('barang.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition 
                {{ request()->routeIs('barang.index') ? 'bg-white/20 font-semibold shadow-inner' : 'hover:bg-white/10' }}">
                <i class="fi fi-rr-box"></i>
                <span>Barang</span>
            </a>
            @endcan

            <a href="{{ route('transaksi.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition 
                {{ request()->routeIs('transaksi.index') ? 'bg-white/20 font-semibold shadow-inner' : 'hover:bg-white/10' }}">
                <i class="fi fi-rr-exchange"></i>
                <span>Transaksi</span>
            </a>

            @can('role-A')
            <a href="{{ route('laporan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition 
                {{ request()->routeIs('laporan.index') ? 'bg-white/20 font-semibold shadow-inner' : 'hover:bg-white/10' }}">
                <i class="fi fi-rr-document"></i>
                <span>Laporan</span>
            </a>
            @endcan
        </nav>

        <!-- User Info + Logout -->
        <div class="border-t border-green-500/40 p-4 space-y-2">
            <div>
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="text-sm text-green-200">{{ Auth::user()->email }}</div>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}"
                    class="block w-full text-left px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition">
                    <i class="fi fi-rr-user mr-2"></i> Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white transition">
                        <i class="fi fi-rr-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Content Area -->
    <div class="flex-1 flex flex-col">
        <!-- Top bar -->
        <header class="h-16 bg-white dark:bg-gray-800 border-b flex items-center justify-between px-6 shadow-sm">
            <div class="text-lg font-bold text-gray-800 dark:text-gray-100">
                Sistem Inventaris <span class="text-green-600">Artha Jaya Mandiri</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="hidden sm:inline text-gray-700 dark:text-gray-300 font-medium">
                    {{ Auth::user()->name }}
                </span>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=10b981&color=fff"
                    class="w-9 h-9 rounded-full shadow-md border-2 border-green-500"
                    alt="avatar">
            </div>
        </header>

        <!-- Main content -->
        <main class="p-6 overflow-y-auto bg-gray-50 dark:bg-gray-900">
            {{ $slot }}
        </main>
    </div>
</div>
