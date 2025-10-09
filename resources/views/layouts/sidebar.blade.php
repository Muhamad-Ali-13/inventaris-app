<!-- resources/views/layouts/sidebar.blade.php -->
<div class="flex h-screen bg-white">
    <!-- Sidebar -->
    <aside
        class="hidden md:flex w-64 text-white flex-col bg-gradient-to-b from-emerald-600 via-green-700 to-green-900 shadow-lg">

        <!-- Logo -->
        <div class="h-16 flex items-center justify-center border-b border-green-500/40">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <x-application-logo class="block h-8 w-auto text-white" />
                <span class="font-bold text-lg tracking-wide">SIAJM</span>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
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
            @endcan
            <a href="{{ route('barang.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition 
                {{ request()->routeIs('barang.index') ? 'bg-white/20 font-semibold shadow-inner' : 'hover:bg-white/10' }}">
                <i class="fi fi-rr-box"></i>
                <span>Barang</span>
            </a>

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

        <!-- User Info -->
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

    <!-- Content -->
    <div class="flex-1 flex flex-col bg-white">
        <!-- Top bar -->
        <header class="h-16 bg-white border-b flex items-center justify-between px-6 shadow-sm">
            <div class="text-lg font-bold text-gray-700">
                Sistem Inventaris <span class="text-green-600">Artha Jaya Mandiri</span>
            </div>

            <!-- Desktop view (nama + avatar) -->
            <div class="hidden sm:flex items-center gap-4">
                <span class="text-gray-600 font-medium">
                    {{ Auth::user()->name }}
                </span>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=10b981&color=fff"
                    class="w-9 h-9 rounded-full shadow-md border-2 border-green-500" alt="avatar">
            </div>

            <!-- Mobile view (dropdown dengan animasi) -->
            <div class="relative sm:hidden" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=10b981&color=fff"
                        class="w-9 h-9 rounded-full shadow-md border-2 border-green-500" alt="avatar">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown menu -->
                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                    x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 z-50">

                    <div class="px-4 py-3 border-b">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="p-6 overflow-y-auto bg-white">
            {{ $slot }}
        </main>
    </div>
</div>

<!-- Mobile bottom nav -->
<div
    class="fixed bottom-0 left-0 right-0 bg-white text-gray-700 flex justify-around items-center py-2 md:hidden z-50
           pb-[env(safe-area-inset-bottom)] backdrop-blur-sm border-t shadow-md">

    <a href="{{ route('dashboard') }}"
        class="flex flex-col items-center {{ request()->routeIs('dashboard') ? 'text-green-600 font-semibold' : '' }}">
        <i class="fi fi-rr-home text-xl"></i>
        <span class="text-[10px] sm:text-xs">Dashboard</span>
    </a>
    @can('role-A')
        <a href="{{ route('karyawans.index') }}"
            class="flex flex-col items-center {{ request()->routeIs('karyawans.index') ? 'text-green-600 font-semibold' : '' }}">
            <i class="fi fi-rr-people text-xl"></i>
            <span class="text-[10px] sm:text-xs">Karyawan</span>
        </a>
        <a href="{{ route('departemen.index') }}"
            class="flex flex-col items-center {{ request()->routeIs('departemen.index') ? 'text-green-600 font-semibold' : '' }}">
            <i class="fi fi-rr-building text-xl"></i>
            <span class="text-[10px] sm:text-xs">Departemen</span>
        </a>
        <a href="{{ route('kategori.index') }}"
            class="flex flex-col items-center {{ request()->routeIs('kategori.index') ? 'text-green-600 font-semibold' : '' }}">
            <i class="fi fi-rr-layers text-xl"></i>
            <span class="text-[10px] sm:text-xs">Kategori</span>
        </a>
    @endcan

    <a href="{{ route('barang.index') }}"
        class="flex flex-col items-center {{ request()->routeIs('barang.index') ? 'text-green-600 font-semibold' : '' }}">
        <i class="fi fi-rr-box text-xl"></i>
        <span class="text-[10px] sm:text-xs">Barang</span>
    </a>

    <a href="{{ route('transaksi.index') }}"
        class="flex flex-col items-center {{ request()->routeIs('transaksi.index') ? 'text-green-600 font-semibold' : '' }}">
        <i class="fi fi-rr-exchange text-xl"></i>
        <span class="text-[10px] sm:text-xs">Transaksi</span>
    </a>

    <a href="{{ route('profile.edit') }}"
        class="flex flex-col items-center {{ request()->routeIs('profile.edit') ? 'text-green-600 font-semibold' : '' }}">
        <i class="fi fi-rr-user text-xl"></i>
        <span class="text-[10px] sm:text-xs">Profile</span>
    </a>
</div>
