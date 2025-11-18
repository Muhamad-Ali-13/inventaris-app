<div x-data="{ openSidebar: false, showContent: false }" x-init="setTimeout(() => showContent = true, 100)" class="flex h-screen bg-white text-green-700">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- SIDEBAR -->
    <aside :class="openSidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        class="fixed md:static top-0 left-0 w-64 h-full flex flex-col bg-white border-r border-green-200 shadow-xl transform transition-all duration-300 ease-in-out z-50">

        <!-- Logo -->
        <div
            class="h-16 flex items-center justify-center border-b border-green-200 bg-gradient-to-r from-green-50 to-white shadow-sm">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 transition-transform hover:scale-105">
                <x-application-logo class="block h-9 w-auto text-green-700" />
                <span class="font-extrabold text-lg tracking-wide text-green-700">SIAJM</span>
            </a>
        </div>

        <!-- Info User -->
        <div class="flex items-center gap-3 mb-4 p-3 bg-green-50 rounded-lg shadow-inner mt-4 mx-3">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=16a34a&color=fff"
                class="w-10 h-10 rounded-full border-2 border-green-400 shadow-sm" alt="avatar">
            <div>
                <div class="font-semibold text-green-800">{{ Auth::user()->name }}</div>
                <div class="text-xs text-green-500">{{ Auth::user()->email }}</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200
                {{ request()->routeIs('dashboard') ? 'bg-green-100 font-semibold text-green-800 shadow-inner ring-2 ring-green-300' : 'hover:bg-green-50 hover:translate-x-1' }}">
                <i class="fi fi-rr-home"></i>
                <span>Dashboard</span>
            </a>

            @can('role-A')
                <a href="{{ route('karyawans.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200
                {{ request()->routeIs('karyawans.*') ? 'bg-green-100 font-semibold text-green-800 shadow-inner ring-2 ring-green-300' : 'hover:bg-green-50 hover:translate-x-1' }}">
                    <i class="fi fi-rr-people"></i>
                    <span>Karyawan</span>
                </a>
            @endcan

            {{-- MASTER DATA: Hanya Admin --}}
            @can('role-A')
                <div x-data="{ openMaster: {{ request()->routeIs('departemen.*', 'kategori.*', 'barang.*') ? 'true' : 'false' }} }">
                    <button @click="openMaster = !openMaster"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition-all duration-200 hover:bg-green-50">
                        <div class="flex items-center gap-3">
                            <i class="fi fi-rr-database"></i>
                            <span>Master Data</span>
                        </div>
                        <i :class="openMaster ? 'fi fi-rr-angle-small-up' : 'fi fi-rr-angle-small-down'"></i>
                    </button>

                    <div x-show="openMaster" x-transition class="pl-8 space-y-1 mt-1">
                        <a href="{{ route('departemen.index') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-all duration-200
                {{ request()->routeIs('departemen.*') ? 'bg-green-100 font-semibold text-green-800 ring-1 ring-green-300' : 'hover:bg-green-50' }}">
                            <i class="fi fi-rr-building"></i>
                            <span>Departemen</span>
                        </a>

                        <a href="{{ route('kategori.index') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-all duration-200
                {{ request()->routeIs('kategori.*') ? 'bg-green-100 font-semibold text-green-800 ring-1 ring-green-300' : 'hover:bg-green-50' }}">
                            <i class="fi fi-rr-layers"></i>
                            <span>Kategori</span>
                        </a>

                        <a href="{{ route('barang.index') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-all duration-200
                {{ request()->routeIs('barang.*') ? 'bg-green-100 font-semibold text-green-800 ring-1 ring-green-300' : 'hover:bg-green-50' }}">
                            <i class="fi fi-rr-box"></i>
                            <span>Barang</span>
                        </a>
                    </div>
                </div>
            @endcan


            <a href="{{ route('stok.barang') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 
        {{ request()->routeIs('stok.barang') ? 'bg-green-100 font-semibold text-green-800 shadow-inner ring-2 ring-green-300' : 'hover:bg-green-50 hover:translate-x-1' }}">
                <i class="fi fi-rr-box"></i> <span>Stok Barang</span>
            </a>

            {{-- TRANSAKSI: Admin, Direktur, Karyawan --}}
            <div x-data="{ openTransaksi: {{ request()->routeIs('pemasukan.*', 'pengeluaran.*') ? 'true' : 'false' }} }">
                <button @click="openTransaksi = !openTransaksi"
                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition-all duration-200 hover:bg-green-50">
                    <div class="flex items-center gap-3">
                        <i class="fi fi-rr-exchange"></i>
                        <span>Transaksi</span>
                    </div>
                    <i :class="openTransaksi ? 'fi fi-rr-angle-small-up' : 'fi fi-rr-angle-small-down'"></i>
                </button>

                <div x-show="openTransaksi" x-transition class="pl-8 space-y-1 mt-1">
                    @can('role-A')
                        <a href="{{ route('pemasukan.index') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-all duration-200
                    {{ request()->routeIs('pemasukan.*') ? 'bg-green-100 font-semibold text-green-800 ring-1 ring-green-300' : 'hover:bg-green-50' }}">
                            <i class="fi fi-rr-arrow-trend-up"></i>
                            <span>Pemasukan</span>
                        </a>
                    @endcan

                    <a href="{{ route('pengeluaran.index') }}"
                        class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-all duration-200
                {{ request()->routeIs('pengeluaran.*') ? 'bg-green-100 font-semibold text-green-800 ring-1 ring-green-300' : 'hover:bg-green-50' }}">
                        <i class="fi fi-rr-arrow-trend-down"></i>
                        <span>Pengeluaran</span>
                    </a>
                </div>
            </div>

            {{-- LAPORAN: Admin + Direktur --}}
            @canany(['role-A', 'role-D'])
                <div x-data="{ openLaporan: {{ request()->routeIs('laporan.*','akuntansi.*') ? 'true' : 'false' }} }">
                    <button @click="openLaporan = !openLaporan"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition-all duration-200 hover:bg-green-50">
                        <div class="flex items-center gap-3">
                            <i class="fi fi-rr-document"></i>
                            <span>Laporan</span>
                        </div>
                        <i :class="openLaporan ? 'fi fi-rr-angle-small-up' : 'fi fi-rr-angle-small-down'"></i>
                    </button>
                    <div x-show="openLaporan" x-transition class="pl-8 space-y-1 mt-1">
                        <a href="{{ route('laporan.index') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-all duration-200
        {{ request()->routeIs('laporan.*') ? 'bg-green-100 font-semibold text-green-800 shadow-inner ring-2 ring-green-300' : 'hover:bg-green-50 hover:translate-x-1' }}">
                            <i class="fi fi-rr-box"></i>
                            <span>Barang</span>
                        </a>
                        <a href="{{ route('akuntansi.index') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('akuntansi.*') ? 'bg-green-100 font-semibold text-green-800 shadow-inner ring-2 ring-green-300' : 'hover:bg-green-50 hover:translate-x-1' }}">
                            <i class="fi fi-rr-chart-pie"></i>
                            Akuntansi
                        </a>

                    </div>
                </div>
            @endcanany

        </nav>
    </aside>

    <!-- Overlay (Mobile) -->
    <div x-show="openSidebar" @click="openSidebar = false"
        class="fixed inset-0 bg-black bg-opacity-40 md:hidden z-40 transition-opacity duration-300 ease-in-out"></div>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col">
        <!-- HEADER -->
        <header
            class="backdrop-blur-md bg-white/90 border-b border-green-200 flex items-center justify-between px-4 sm:px-6 h-16 sticky top-0 z-30 shadow-md">
            <!-- Kiri -->
            <div class="flex items-center gap-3">
                <button @click="openSidebar = !openSidebar"
                    class="md:hidden text-green-700 hover:text-green-900 focus:outline-none flex items-center justify-center w-10 h-10 transition-all hover:scale-110">
                    <i class="fi fi-rr-menu-burger text-2xl"></i>
                </button>

                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-green-600 text-white flex items-center justify-center rounded-lg shadow-md">
                        <i class="fi fi-rr-boxes text-lg"></i>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-green-800 font-bold text-sm sm:text-base">Sistem Inventaris</span>
                        <span class="text-green-500 font-semibold text-xs sm:text-sm">Artha Jaya Mandiri</span>
                    </div>
                </div>
            </div>

            <!-- Kanan: Profile -->
            <div x-data="{ open: false }" class="relative flex items-center gap-3">
                <button @click="open = !open"
                    class="flex items-center gap-2 focus:outline-none transition-all hover:scale-105">
                    <span class="hidden sm:block text-green-800 text-sm sm:text-base font-medium">
                        {{ Auth::user()->name }}
                    </span>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=16a34a&color=fff"
                        class="w-9 h-9 rounded-full border-2 border-green-500 shadow-md" alt="avatar">
                    <i class="fi fi-rr-angle-small-down text-green-600 text-lg"></i>
                </button>

                <!-- Dropdown -->
                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute right-0 top-12 w-52 bg-white border border-green-100 rounded-xl shadow-lg overflow-hidden z-40">
                    <div class="px-4 py-2 border-b border-green-100">
                        <p class="text-green-800 font-semibold text-sm">{{ Auth::user()->name }}</p>
                        <p class="text-green-500 text-xs">{{ Auth::user()->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}"
                        class="block px-4 py-2 text-sm text-green-700 hover:bg-green-50 transition-all">
                        <i class="fi fi-rr-user mr-2"></i> Profil Saya
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-all">
                            <i class="fi fi-rr-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main x-show="showContent" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="flex-1 p-6 overflow-y-auto bg-white text-green-700">
            {{ $slot }}
        </main>
    </div>
</div>
