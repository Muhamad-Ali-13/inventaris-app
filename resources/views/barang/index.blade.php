<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="gap-5 items-start flex">
                <!-- FORM INPUT -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg w-1/2 p-4">
                    <div class="p-4 bg-gray-100 mb-2 rounded-xl font-bold">
                        FORM INPUT BARANG
                    </div>
                    <form method="POST" action="{{ route('barang.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_barang" class="block text-sm font-medium">Nama Barang</label>
                            <input type="text" name="nama_barang" id="nama_barang" required
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white" />
                        </div>

                        <div class="mb-3">
                            <label for="kategori_id" class="block text-sm font-medium">Kategori</label>
                            <select name="kategori_id" id="kategori_id" required
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategori as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="stok" class="block text-sm font-medium">Stok</label>
                            <input type="number" name="stok" id="stok" required min="0"
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white" />
                        </div>

                        <div class="mb-3">
                            <label for="satuan" class="block text-sm font-medium">Satuan</label>
                            <input type="text" name="satuan" id="satuan" required
                                placeholder="Contoh: pcs, box, unit"
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white" />
                        </div>

                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Simpan
                        </button>
                    </form>
                </div>

                <!-- TABEL DATA -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg w-full p-4">
                    <div class="p-4 bg-gray-100 mb-2 rounded-xl font-bold">
                        DATA BARANG
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3">NO</th>
                                    <th class="px-6 py-3">NAMA BARANG</th>
                                    <th class="px-6 py-3">KATEGORI</th>
                                    <th class="px-6 py-3">STOK</th>
                                    <th class="px-6 py-3">SATUAN</th>
                                    <th class="px-6 py-3">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no=1; @endphp
                                @foreach ($barang as $b)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4">{{ $no++ }}</td>
                                        <td class="px-6 py-4">{{ $b->nama_barang }}</td>
                                        <td class="px-6 py-4">{{ $b->kategori->nama_kategori }}</td>
                                        <td class="px-6 py-4">{{ $b->stok }}</td>
                                        <td class="px-6 py-4">{{ $b->satuan }}</td>
                                        <td class="px-6 py-4">
                                            <button type="button" class="bg-yellow-500 text-white px-3 py-1 rounded"
                                                onclick="editBarangModal(this)" 
                                                data-id="{{ $b->id }}" 
                                                data-nama="{{ $b->nama_barang }}"
                                                data-kategori="{{ $b->kategori_id }}"
                                                data-stok="{{ $b->stok }}"
                                                data-satuan="{{ $b->satuan }}">
                                                Edit
                                            </button>
                                            <form action="{{ route('barang.destroy',$b->id) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 text-white px-3 py-1 rounded">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="fixed inset-0 flex items-center justify-center z-50 hidden" id="barangModal">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white rounded-lg shadow w-full max-w-lg mx-5">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold">Edit Barang</h3>
                <button type="button" onclick="barangModalClose()" class="text-gray-400 hover:text-gray-900">✕</button>
            </div>
            <form method="POST" id="formBarangModal">
                @csrf
                <div class="p-4 space-y-3">
                    <input type="hidden" name="_method" value="PATCH">
                    <div>
                        <label for="edit_nama_barang" class="block text-sm font-medium">Nama Barang</label>
                        <input type="text" name="nama_barang" id="edit_nama_barang" required
                            class="w-full rounded-lg border-gray-300" />
                    </div>
                    <div>
                        <label for="edit_kategori_id" class="block text-sm font-medium">Kategori</label>
                        <select name="kategori_id" id="edit_kategori_id" required
                            class="w-full rounded-lg border-gray-300">
                            @foreach ($kategori as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="edit_stok" class="block text-sm font-medium">Stok</label>
                        <input type="number" name="stok" id="edit_stok" min="0" required
                            class="w-full rounded-lg border-gray-300" />
                    </div>
                    <div>
                        <label for="edit_satuan" class="block text-sm font-medium">Satuan</label>
                        <input type="text" name="satuan" id="edit_satuan" required
                            class="w-full rounded-lg border-gray-300" />
                    </div>
                </div>
                <div class="flex justify-end p-4 border-t">
                    <button type="button" onclick="barangModalClose()" class="bg-gray-300 px-4 py-2 rounded mr-2">Batal</button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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
