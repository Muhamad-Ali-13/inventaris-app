<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <div class="py-10 bg-gradient-to-br from-gray-50 via-white to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">

                <!-- HEADER -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">📦 Stok Barang</h3>
                        <p class="text-gray-500 text-sm">Lihat daftar dan stok barang yang tersedia di gudang.</p>
                    </div>
                </div>
                {{-- PERUBAHAN: Membungkus filter dalam form GET dengan desain panel --}}
                <form action="{{ route('barang.index') }}" method="GET">
                    {{-- Input tersembunyi untuk mempertahankan sorting --}}
                    @if (request('sort_by'))
                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    @endif
                    @if (request('order'))
                        <input type="hidden" name="order" value="{{ request('order') }}">
                    @endif

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Kiri: Filter Entries dan Kategori -->
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Entries -->
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <label for="entries">Tampilkan</label>
                                <select name="entries" id="entries"
                                    class="border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500 bg-white"
                                    onchange="this.form.submit()">
                                    <option value="5" {{ request('entries') == 5 ? 'selected' : '' }}>5
                                    </option>
                                    <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10
                                    </option>
                                    <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25
                                    </option>
                                    <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50
                                    </option>
                                    <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100
                                    </option>
                                </select>
                                <span>entri</span>
                            </div>

                            <!-- Filter kategori -->
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <label for="kategoriFilter">Filter</label>
                                <select name="kategori_id" id="kategoriFilter"
                                    class="border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500 bg-white"
                                    onchange="this.form.submit()">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($kategori as $k)
                                        <option value="{{ $k->id }}"
                                            {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Kanan: Search Bar -->
                        <div class="relative w-full lg:w-96">
                            <input type="text" name="search" placeholder="Cari nama atau kode barang..."
                                value="{{ request('search') }}"
                                class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-green-500 focus:border-green-500 bg-white">
                            <i class="fi fi-rr-search absolute left-3 top-2.5 text-gray-400"></i>
                        </div>
                    </div>
                </form>

                <!-- DESKTOP TABLE -->
                <div class="overflow-x-auto mt-5 rounded-lg border border-gray-100 hidden md:block">
                    <table class="w-full text-sm text-left text-gray-700" id="barangTable">
                        <thead class="bg-green-100 text-green-800 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">No</th>
                                <th class="px-6 py-3">Kode Barang</th>
                                <th class="px-6 py-3">Nama Barang</th>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3">Stok</th>
                                <th class="px-6 py-3">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barang as $index => $b)
                                <tr class="border-b hover:bg-green-50 transition">
                                    <td class="px-6 py-3">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3">{{ $b->kode_barang }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $b->nama_barang }}</td>
                                    <td class="px-6 py-3">{{ $b->kategori->nama_kategori }}</td>
                                    <td
                                        class="px-6 py-3 {{ $b->qty == 0 ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                                        {{ $b->qty }}</td>
                                    <td class="px-6 py-3">{{ $b->satuan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD VIEW -->
                <div class="md:hidden space-y-4" id="barangCards">
                    @foreach ($barang as $b)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-lg text-gray-800">{{ $b->nama_barang }}</h4>
                                <span
                                    class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">{{ $b->kategori->nama_kategori }}</span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>Stok:</strong>
                                    <span
                                        class="{{ $b->stok == 0 ? 'text-red-500 font-semibold' : 'text-gray-800' }}">{{ $b->stok }}</span>
                                </p>
                                <p><strong>Satuan:</strong> {{ $b->satuan }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- PERUBAHAN: Tambahkan Pagination --}}
                <div class="mt-6 flex justify-center">
                    {{ $barang->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.getElementById("searchInput");
            const kategoriFilter = document.getElementById("kategoriFilter");
            const entriesSelect = document.getElementById("entries");
            const table = document.getElementById("barangTable");
            const rows = Array.from(table.querySelectorAll("tbody tr"));

            function filterTable() {
                const search = searchInput.value.toLowerCase();
                const kategori = kategoriFilter.value.toLowerCase();
                const limit = parseInt(entriesSelect.value);

                let count = 0;
                rows.forEach(row => {
                    const nama = row.cells[1].innerText.toLowerCase();
                    const kat = row.cells[2].innerText.toLowerCase();
                    if (nama.includes(search) && (kategori === "" || kat === kategori) && count < limit) {
                        row.style.display = "";
                        count++;
                    } else {
                        row.style.display = "none";
                    }
                });
            }

            searchInput.addEventListener("input", filterTable);
            kategoriFilter.addEventListener("change", filterTable);
            entriesSelect.addEventListener("change", filterTable);
            filterTable();
        });
    </script>

</x-app-layout>
