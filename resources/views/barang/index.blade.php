<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Manajemen Barang') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">


                <!-- HEADER + BUTTON -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Data Barang</h3>
                    @can('role-A')
                        <button type="button" onclick="tambahBarangModal()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg shadow-sm transition">
                            + Tambah Barang
                        </button>
                    @endcan
                </div>


                <!-- TABEL DATA -->
                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
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
                            @php $no=1; @endphp
                            @foreach ($barang as $b)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-3">{{ $no++ }}</td>
                                    <td class="px-6 py-3">{{ $b->nama_barang }}</td>
                                    <td class="px-6 py-3">{{ $b->kategori->nama_kategori }}</td>
                                    <td class="px-6 py-3">{{ $b->stok }}</td>
                                    <td class="px-6 py-3">{{ $b->satuan }}</td>
                                    @can('role-A')
                                        <td class="px-6 py-3 text-center flex justify-center gap-2">
                                            <button type="button"
                                                class="bg-amber-400 hover:bg-amber-500 text-white p-2.5 rounded-lg shadow-sm"
                                                onclick="editBarangModal(this)" data-id="{{ $b->id }}"
                                                data-nama="{{ $b->nama_barang }}" data-kategori="{{ $b->kategori_id }}"
                                                data-stok="{{ $b->stok }}" data-satuan="{{ $b->satuan }}">
                                                <i class="fi fi-sr-file-edit"></i>
                                            </button>

                                            <form action="{{ route('barang.destroy', $b->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 hover:bg-red-600 text-white p-2.5 rounded-lg shadow-sm">
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
    </script>
</x-app-layout>
