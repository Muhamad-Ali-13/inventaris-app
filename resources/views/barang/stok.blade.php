<x-app-layout>

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

                <!-- FILTERS + SEARCH + ENTRIES -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Entries -->
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            Tampilkan
                            <select id="entries"
                                class="border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            entri
                        </label>

                        <!-- Filter kategori -->
                        <select id="kategoriFilter"
                            class="border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategori as $k)
                                <option value="{{ $k->nama_kategori }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="searchInput" placeholder="Cari nama barang..."
                            class="w-full border border-gray-300 rounded-lg pl-10 text-sm focus:ring-green-500 focus:border-green-500">
                        <i class="fi fi-rr-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>

                <!-- DESKTOP TABLE -->
                <div class="overflow-x-auto rounded-lg border border-gray-100 hidden md:block">
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
