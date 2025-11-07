<x-app-layout>

    <div class="py-10 bg-gradient-to-br from-gray-50 via-white to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">

                <!-- HEADER + TOOLS -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">📦 Data Barang</h3>
                        <p class="text-gray-500 text-sm">Kelola dan pantau stok barang secara real-time.</p>
                    </div>

                    @can('role-A')
                        <button onclick="tambahBarangModal()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg shadow transition text-sm font-semibold">
                            + Tambah Barang
                        </button>
                    @endcan
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
                                <th class="px-6 py-3">Nama Barang</th>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3">Stok</th>
                                <th class="px-6 py-3">Satuan</th>
                                @can('role-A')
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barang as $index => $b)
                                <tr class="border-b hover:bg-green-50 transition">
                                    <!-- Nomor tetap berurutan sesuai tabel -->
                                    <td class="px-4 py-2">
                                        {{ $barang->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $b->nama_barang }}</td>
                                    <td class="px-6 py-3">{{ $b->kategori->nama_kategori }}</td>
                                    <td
                                        class="px-6 py-3 {{ $b->stok == 0 ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                                        {{ $b->stok }}</td>
                                    <td class="px-6 py-3">{{ $b->satuan }}</td>
                                    @can('role-A')
                                        <td class="px-6 py-3 text-center flex justify-center gap-2">
                                            <button onclick="editBarangModal(this)" data-id="{{ $b->id }}"
                                                data-nama="{{ $b->nama_barang }}" data-kategori="{{ $b->kategori_id }}"
                                                data-stok="{{ $b->stok }}" data-satuan="{{ $b->satuan }}"
                                                title="Edit" class="text-blue-600 hover:text-blue-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" class="w-5 h-5 inline">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </button>

                                            <form action="{{ route('barang.destroy', $b->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf @method('DELETE')
                                                <button title="Hapus" class="text-red-600 hover:text-red-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3m-7 0h8" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    @endcan
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
                                <p><strong>Stok:</strong> <span
                                        class="{{ $b->stok == 0 ? 'text-red-500 font-semibold' : 'text-gray-800' }}">{{ $b->stok }}</span>
                                </p>
                                <p><strong>Satuan:</strong> {{ $b->satuan }}</p>
                            </div>
                            @can('role-A')
                                <div class="flex gap-2 mt-4">
                                    <button onclick="editBarangModal(this)" data-id="{{ $b->id }}"
                                        data-nama="{{ $b->nama_barang }}" data-kategori="{{ $b->kategori_id }}"
                                        data-stok="{{ $b->stok }}" data-satuan="{{ $b->satuan }}" title="Edit"
                                        class="text-blue-600 hover:text-blue-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-5 h-5 inline">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('barang.destroy', $b->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="flex-1">
                                        @csrf @method('DELETE')
                                        <button title="Hapus" class="text-red-600 hover:text-red-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3m-7 0h8" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH -->
    <div class="fixed inset-0 flex items-center justify-center z-50 hidden" id="tambahBarangModal">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative z-10">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Tambah Barang</h3>
            </div>

            <form method="POST" action="{{ route('barang.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="nama_barang" class="block text-sm font-medium text-gray-600 mb-1">Nama Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang" required
                        placeholder="Contoh: Pulpen, Buku, Monitor"
                        class="w-full border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg px-3 py-2 text-gray-800 placeholder-gray-400 transition duration-200" />
                </div>
                <div>
                    <label for="kategori_id" class="block text-sm font-medium text-gray-600 mb-1">Kategori</label>
                    <select name="kategori_id" id="kategori_id" required
                        class="w-full border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg px-3 py-2 text-gray-800 transition duration-200">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="stok" class="block text-sm font-medium text-gray-600 mb-1">Stok</label>
                        <input type="number" name="stok" id="stok" required min="0"
                            class="w-full border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg px-3 py-2 text-gray-800 transition duration-200" />
                    </div>
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-600 mb-1">Satuan</label>
                        <input type="text" name="satuan" id="satuan" required
                            placeholder="Contoh: pcs, box, unit"
                            class="w-full border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg px-3 py-2 text-gray-800 transition duration-200" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-3 border-t">
                    <button type="button" onclick="tambahBarangModalClose()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium px-5 py-2 rounded-lg">Batal</button>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="fixed inset-0 flex items-center justify-center z-50 hidden" id="barangModal">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative z-10">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Edit Barang</h3>
            </div>

            <form method="POST" id="formBarangModal" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div>
                    <label for="edit_nama_barang" class="block text-sm font-medium text-gray-600 mb-1">Nama
                        Barang</label>
                    <input type="text" name="nama_barang" id="edit_nama_barang" required
                        class="w-full border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg px-3 py-2 text-gray-800 transition duration-200" />
                </div>
                <div>
                    <label for="edit_kategori_id"
                        class="block text-sm font-medium text-gray-600 mb-1">Kategori</label>
                    <select name="kategori_id" id="edit_kategori_id" required
                        class="w-full border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg px-3 py-2 text-gray-800 transition duration-200">
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_stok" class="block text-sm font-medium text-gray-600 mb-1">Stok</label>
                        <input type="number" name="stok" id="edit_stok" min="0" required
                            class="w-full border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg px-3 py-2 text-gray-800 transition duration-200" />
                    </div>
                    <div>
                        <label for="edit_satuan" class="block text-sm font-medium text-gray-600 mb-1">Satuan</label>
                        <input type="text" name="satuan" id="edit_satuan" required
                            class="w-full border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg px-3 py-2 text-gray-800 transition duration-200" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-3 border-t">
                    <button type="button" onclick="barangModalClose()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium px-5 py-2 rounded-lg">Batal</button>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-lg">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT -->
    <script>
        // MODAL
        function tambahBarangModal() {
            document.getElementById('tambahBarangModal').classList.remove('hidden');
        }

        function tambahBarangModalClose() {
            document.getElementById('tambahBarangModal').classList.add('hidden');
        }

        function editBarangModal(button) {
            const id = button.dataset.id;
            const nama = button.dataset.nama;
            const kategori = button.dataset.kategori;
            const stok = button.dataset.stok;
            const satuan = button.dataset.satuan;

            document.getElementById('formBarangModal').setAttribute('action', "{{ route('barang.update', ':id') }}"
                .replace(':id', id));
            document.getElementById('edit_nama_barang').value = nama;
            document.getElementById('edit_kategori_id').value = kategori;
            document.getElementById('edit_stok').value = stok;
            document.getElementById('edit_satuan').value = satuan;

            document.getElementById('barangModal').classList.remove('hidden');
        }

        function barangModalClose() {
            document.getElementById('barangModal').classList.add('hidden');
        }

        // SEARCH, FILTER & ENTRIES (client-side)
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
