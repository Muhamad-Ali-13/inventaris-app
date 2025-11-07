<x-app-layout>

    <div class="py-10 bg-gradient-to-br from-gray-50 via-white to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">

                <!-- HEADER + TOOLS -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">🏷️ Data Kategori</h3>
                        <p class="text-gray-500 text-sm">Kelola kategori barang Anda dengan mudah.</p>
                    </div>

                    <button onclick="tambahKategoriModal()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg shadow transition text-sm font-semibold">
                        + Tambah Kategori
                    </button>
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
                            </select>
                            entri
                        </label>
                    </div>

                    <!-- Search -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="searchInput" placeholder="Cari kategori..."
                            class="w-full border border-gray-300 rounded-lg pl-10 text-sm focus:ring-green-500 focus:border-green-500">
                        <i class="fi fi-rr-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>

                <!-- DESKTOP TABLE -->
                <div class="overflow-x-auto rounded-lg border border-gray-100 hidden md:block">
                    <table class="w-full text-sm text-left text-gray-700" id="kategoriTable">
                        <thead class="bg-green-100 text-green-800 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3 text-center">No</th>
                                <th class="px-6 py-3">Nama Kategori</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategori as $index => $k)
                                <tr class="border-b hover:bg-green-50 transition">
                                    <td class="px-6 py-3 text-center">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $k->nama_kategori }}</td>
                                    <td class="px-6 py-3 text-center flex justify-center gap-2">
                                        <button onclick="editKategoriModal(this)" data-id="{{ $k->id }}"
                                            data-nama="{{ $k->nama_kategori }}" title="Edit"
                                            class="text-blue-600 hover:text-blue-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor" class="w-5 h-5 inline">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>

                                        <form action="{{ route('kategori.destroy', $k->id) }}" method="POST"
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD VIEW -->
                <div class="md:hidden space-y-4" id="kategoriCards">
                    @foreach ($kategori as $k)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-lg text-gray-800">{{ $k->nama_kategori }}</h4>
                            </div>
                            <div class="flex gap-2 mt-2">
                                <button onclick="editKategoriModal(this)" data-id="{{ $k->id }}"
                                    data-nama="{{ $k->nama_kategori }}" title="Edit"
                                    class="text-blue-600 hover:text-blue-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5 inline">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>
                                <form action="{{ route('kategori.destroy', $k->id) }}" method="POST"
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
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH -->
    <div class="fixed inset-0 flex items-center justify-center z-50 hidden" id="tambahKategoriModal">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative z-10">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Tambah Kategori</h3>
            </div>

            <form method="POST" action="{{ route('kategori.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="nama_kategori" class="block text-sm font-medium text-gray-600 mb-1">Nama
                        Kategori</label>
                    <input type="text" name="nama_kategori" id="nama_kategori" required placeholder="Contoh: ATK"
                        class="w-full border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg px-3 py-2 text-gray-800 placeholder-gray-400 transition duration-200" />
                </div>
                <div class="flex justify-end gap-3 pt-3 border-t">
                    <button type="button" onclick="tambahKategoriModalClose()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium px-5 py-2 rounded-lg">Batal</button>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="fixed inset-0 flex items-center justify-center z-50 hidden" id="kategoriModal">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative z-10">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Edit Kategori</h3>
            </div>

            <form method="POST" id="formKategoriModal" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div>
                    <label for="edit_nama_kategori" class="block text-sm font-medium text-gray-600 mb-1">Nama
                        Kategori</label>
                    <input type="text" name="nama_kategori" id="edit_nama_kategori" required
                        class="w-full border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg px-3 py-2 text-gray-800 transition duration-200" />
                </div>
                <div class="flex justify-end gap-3 pt-3 border-t">
                    <button type="button" onclick="kategoriModalClose()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium px-5 py-2 rounded-lg">Batal</button>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-lg">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // MODAL
        function tambahKategoriModal() {
            document.getElementById('tambahKategoriModal').classList.remove('hidden');
        }

        function tambahKategoriModalClose() {
            document.getElementById('tambahKategoriModal').classList.add('hidden');
        }

        function editKategoriModal(button) {
            const id = button.dataset.id;
            const nama = button.dataset.nama;

            document.getElementById('formKategoriModal').setAttribute('action', "{{ route('kategori.update', ':id') }}"
                .replace(':id', id));
            document.getElementById('edit_nama_kategori').value = nama;

            document.getElementById('kategoriModal').classList.remove('hidden');
        }

        function kategoriModalClose() {
            document.getElementById('kategoriModal').classList.add('hidden');
        }

        // SEARCH & ENTRIES (client-side)
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.getElementById("searchInput");
            const entriesSelect = document.getElementById("entries");
            const table = document.getElementById("kategoriTable");
            const rows = Array.from(table.querySelectorAll("tbody tr"));

            function filterTable() {
                const search = searchInput.value.toLowerCase();
                const limit = parseInt(entriesSelect.value);
                let count = 0;
                rows.forEach(row => {
                    const nama = row.cells[1].innerText.toLowerCase();
                    if (nama.includes(search) && count < limit) {
                        row.style.display = "";
                        count++;
                    } else {
                        row.style.display = "none";
                    }
                });
            }

            searchInput.addEventListener("input", filterTable);
            entriesSelect.addEventListener("change", filterTable);
            filterTable();
        });
    </script>
</x-app-layout>
