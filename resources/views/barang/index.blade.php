<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Manajemen Barang') }}
        </h2>
    </x-slot>

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

                <!-- FILTERS + SEARCH -->
                <div x-data="{ search: '', kategori: '' }"
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Entries -->
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            Tampilkan
                            <select id="entries"
                                class="border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                                <option>100</option>
                            </select>
                            entri
                        </label>

                        <!-- Filter kategori -->
                        <select x-model="kategori" id="kategoriFilter"
                            class="border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategori as $k)
                                <option value="{{ $k->nama_kategori }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="relative w-full sm:w-64">
                        <input x-model="search" type="text" placeholder="Cari nama barang..."
                            class="w-full border-gray-300 rounded-lg pl-10 text-sm focus:ring-green-500 focus:border-green-500">
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
                            @php $no = 1; @endphp
                            @foreach ($barang as $b)
                                <tr class="border-b hover:bg-green-50 transition">
                                    <td class="px-6 py-3">{{ $no++ }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $b->nama_barang }}</td>
                                    <td class="px-6 py-3">{{ $b->kategori->nama_kategori }}</td>
                                    <td
                                        class="px-6 py-3 {{ $b->stok == 0 ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                                        {{ $b->stok }}</td>
                                    <td class="px-6 py-3">{{ $b->satuan }}</td>
                                    @can('role-A')
                                        <td class="px-6 py-3 text-center flex justify-center gap-2">
                                            <button type="button"
                                                class="bg-amber-400 hover:bg-amber-500 text-white p-2.5 rounded-lg"
                                                onclick="editBarangModal(this)" data-id="{{ $b->id }}"
                                                data-nama="{{ $b->nama_barang }}" data-kategori="{{ $b->kategori_id }}"
                                                data-stok="{{ $b->stok }}" data-satuan="{{ $b->satuan }}">
                                                <i class="fi fi-sr-file-edit"></i>
                                            </button>

                                            <form action="{{ route('barang.destroy', $b->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 hover:bg-red-600 text-white p-2.5 rounded-lg">
                                                    <i class="fi fi-sr-trash"></i>
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
                <div class="md:hidden space-y-4">
                    @foreach ($barang as $b)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-lg text-gray-800">{{ $b->nama_barang }}</h4>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">
                                    {{ $b->kategori->nama_kategori }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>Stok:</strong>
                                    <span
                                        class="{{ $b->stok == 0 ? 'text-red-500 font-semibold' : 'text-gray-800' }}">{{ $b->stok }}</span>
                                </p>
                                <p><strong>Satuan:</strong> {{ $b->satuan }}</p>
                            </div>
                            @can('role-A')
                                <div class="flex gap-2 mt-4">
                                    <button type="button"
                                        class="flex-1 bg-amber-400 hover:bg-amber-500 text-white py-2 rounded-lg text-sm font-medium"
                                        onclick="editBarangModal(this)" data-id="{{ $b->id }}"
                                        data-nama="{{ $b->nama_barang }}" data-kategori="{{ $b->kategori_id }}"
                                        data-stok="{{ $b->stok }}" data-satuan="{{ $b->satuan }}">
                                        <i class="fi fi-sr-file-edit mr-1"></i> Edit
                                    </button>
                                    <form action="{{ route('barang.destroy', $b->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="flex-1">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg text-sm font-medium">
                                            <i class="fi fi-sr-trash mr-1"></i> Hapus
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
                <button onclick="tambahBarangModalClose()" class="text-gray-500 hover:text-gray-800 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('barang.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="nama_barang" class="block text-sm font-medium text-gray-600 mb-1">Nama Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang" required
                        placeholder="Contoh: Pulpen, Buku, Monitor"
                        class="w-full border border-gray-300 focus:ring-2 focus:ring-indigo-400 
                               focus:border-indigo-400 rounded-lg px-3 py-2 text-gray-800 placeholder-gray-400 transition duration-200" />
                </div>

                <div>
                    <label for="kategori_id" class="block text-sm font-medium text-gray-600 mb-1">Kategori</label>
                    <select name="kategori_id" id="kategori_id" required
                        class="w-full border border-gray-300 focus:ring-2 focus:ring-indigo-400 
                               focus:border-indigo-400 rounded-lg px-3 py-2 text-gray-800 transition duration-200">
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
                            class="w-full border border-gray-300 focus:ring-2 focus:ring-indigo-400 
                                   focus:border-indigo-400 rounded-lg px-3 py-2 text-gray-800 transition duration-200" />
                    </div>
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-600 mb-1">Satuan</label>
                        <input type="text" name="satuan" id="satuan" required
                            placeholder="Contoh: pcs, box, unit"
                            class="w-full border border-gray-300 focus:ring-2 focus:ring-indigo-400 
                                   focus:border-indigo-400 rounded-lg px-3 py-2 text-gray-800 transition duration-200" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t">
                    <button type="button" onclick="tambahBarangModalClose()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium px-5 py-2 rounded-lg">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded-lg">
                        Simpan
                    </button>
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
                <button onclick="barangModalClose()" class="text-gray-500 hover:text-gray-800 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" id="formBarangModal" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div>
                    <label for="edit_nama_barang" class="block text-sm font-medium text-gray-600 mb-1">Nama
                        Barang</label>
                    <input type="text" name="nama_barang" id="edit_nama_barang" required
                        class="w-full border border-gray-300 focus:ring-2 focus:ring-indigo-400 
                               focus:border-indigo-400 rounded-lg px-3 py-2 text-gray-800 transition duration-200" />
                </div>
                <div>
                    <label for="edit_kategori_id"
                        class="block text-sm font-medium text-gray-600 mb-1">Kategori</label>
                    <select name="kategori_id" id="edit_kategori_id" required
                        class="w-full border border-gray-300 focus:ring-2 focus:ring-indigo-400 
                               focus:border-indigo-400 rounded-lg px-3 py-2 text-gray-800 transition duration-200">
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_stok" class="block text-sm font-medium text-gray-600 mb-1">Stok</label>
                        <input type="number" name="stok" id="edit_stok" min="0" required
                            class="w-full border border-gray-300 focus:ring-2 focus:ring-indigo-400 
                                   focus:border-indigo-400 rounded-lg px-3 py-2 text-gray-800 transition duration-200" />
                    </div>
                    <div>
                        <label for="edit_satuan" class="block text-sm font-medium text-gray-600 mb-1">Satuan</label>
                        <input type="text" name="satuan" id="edit_satuan" required
                            class="w-full border border-gray-300 focus:ring-2 focus:ring-indigo-400 
                                   focus:border-indigo-400 rounded-lg px-3 py-2 text-gray-800 transition duration-200" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-3 border-t">
                    <button type="button" onclick="barangModalClose()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium px-5 py-2 rounded-lg">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded-lg">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT -->
    <script>
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

            let url = "{{ route('barang.update', ':id') }}".replace(':id', id);

            document.getElementById('formBarangModal').setAttribute('action', url);
            document.getElementById('edit_nama_barang').value = nama;
            document.getElementById('edit_kategori_id').value = kategori;
            document.getElementById('edit_stok').value = stok;
            document.getElementById('edit_satuan').value = satuan;

            document.getElementById('barangModal').classList.remove('hidden');
        }

        function barangModalClose() {
            document.getElementById('barangModal').classList.add('hidden');
        }

        // SEARCH & FILTER (client-side)
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.querySelector('[x-model="search"]');
            const kategoriFilter = document.getElementById("kategoriFilter");
            const table = document.getElementById("barangTable");
            const rows = table.querySelectorAll("tbody tr");

            function filterTable() {
                const search = searchInput.value.toLowerCase();
                const kategori = kategoriFilter.value.toLowerCase();

                rows.forEach(row => {
                    const nama = row.cells[1].innerText.toLowerCase();
                    const kat = row.cells[2].innerText.toLowerCase();
                    row.style.display =
                        (nama.includes(search) && (kategori === "" || kat === kategori)) ?
                        "" :
                        "none";
                });
            }

            searchInput.addEventListener("input", filterTable);
            kategoriFilter.addEventListener("change", filterTable);
        });
    </script>
</x-app-layout>
