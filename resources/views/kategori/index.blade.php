<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Manajemen Kategori') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- FORM INPUT (DESKTOP) -->
                <div
                    class="hidden md:block bg-white shadow-md rounded-xl p-6 md:w-1/3 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">
                        Tambah Kategori
                    </h3>
                    <form method="POST" action="{{ route('kategori.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="nama_kategori"
                                class="block text-sm font-medium text-gray-600 mb-1">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="nama_kategori"
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-gray-800"
                                placeholder="Contoh: ATK, ETK" required>
                        </div>
                        <button type="submit"
                            class="w-full bg-blue-600 text-white font-medium py-2.5 rounded-lg hover:bg-blue-700 transition">
                            Simpan
                        </button>
                    </form>
                </div>

                <!-- DATA (TABEL DESKTOP / CARD MOBILE) -->
                <div class="bg-white shadow-md rounded-xl p-6 w-full border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Data Kategori</h3>
                        <span class="text-sm text-gray-500">Total: {{ count($kategori) }}</span>
                    </div>

                    <!-- TABEL (DESKTOP) -->
                    <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full text-sm text-gray-700">
                            <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                                <tr>
                                    <th class="px-6 py-3 text-left">No</th>
                                    <th class="px-6 py-3 text-left">Nama Kategori</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php $no = 1; @endphp
                                @foreach ($kategori as $k)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 font-medium text-gray-800">{{ $no++ }}</td>
                                        <td class="px-6 py-4">{{ $k->nama_kategori }}</td>
                                        <td class="px-6 py-4 text-center flex justify-center gap-2">
                                            <button type="button"
                                                class="bg-yellow-400 hover:bg-yellow-500 p-2 rounded-md text-white"
                                                onclick="editKategoriModal(this)" data-modal-target="kategoriModal"
                                                data-id="{{ $k->id }}" data-nama="{{ $k->nama_kategori }}">
                                                <i class="fi fi-sr-file-edit"></i>
                                            </button>
                                            <form action="{{ route('kategori.destroy', $k->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 hover:bg-red-600 p-2 rounded-md text-white">
                                                    <i class="fi fi-sr-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- CARD (MOBILE) -->
                    <div class="grid grid-cols-1 gap-4 md:hidden">
                        @php $no = 1; @endphp
                        @foreach ($kategori as $k)
                            <div class="border border-gray-200 rounded-xl p-4 shadow-sm bg-white">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-semibold text-lg">
                                        {{ $no++ }}. {{ $k->nama_kategori }}
                                    </span>
                                    <div class="flex gap-2">
                                        <button type="button"
                                            class="bg-yellow-400 hover:bg-yellow-500 p-2 rounded-md text-white"
                                            onclick="editKategoriModal(this)" data-modal-target="kategoriModal"
                                            data-id="{{ $k->id }}" data-nama="{{ $k->nama_kategori }}">
                                            <i class="fi fi-sr-file-edit"></i>
                                        </button>
                                        <form action="{{ route('kategori.destroy', $k->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-600 p-2 rounded-md text-white">
                                                <i class="fi fi-sr-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- FLOATING BUTTON (MOBILE) -->
        <button id="btnTambahMobile"
            class="md:hidden fixed bottom-6 right-6 bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 text-2xl z-50"
            onclick="tambahModalOpen()">
            +
        </button>
    </div>

    <!-- MODAL TAMBAH (MOBILE) -->
    <div id="tambahModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm">
        <div class="bg-white rounded-xl w-11/12 max-w-md p-6 shadow-lg">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Tambah Kategori</h3>
                <button onclick="tambahModalClose()" class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
            </div>
            <form method="POST" action="{{ route('kategori.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="nama_kategori_mobile" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Kategori
                    </label>
                    <input type="text" name="nama_kategori" id="nama_kategori_mobile"
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-800"
                        placeholder="Contoh: ATK, ETK" required>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="tambahModalClose()"
                        class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400 text-gray-700 transition">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div id="kategoriModal"
        class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40 backdrop-blur-sm">
        <div class="bg-white w-11/12 md:w-1/3 rounded-xl shadow-lg relative p-6">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Edit Kategori</h3>
                <button type="button" onclick="kategoriModalClose(this)" data-modal-target="kategoriModal"
                    class="text-gray-400 hover:text-gray-700 transition text-xl">&times;</button>
            </div>

            <form method="POST" id="formKategoriModal" class="space-y-4">
                @csrf
                <div>
                    <label for="edit_nama_kategori" class="block text-sm font-medium text-gray-600 mb-1">
                        Nama Kategori
                    </label>
                    <input type="text" name="nama_kategori" id="edit_nama_kategori"
                        class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-gray-800"
                        required>
                </div>
                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" onclick="kategoriModalClose(this)" data-modal-target="kategoriModal"
                        class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400 text-gray-700 transition">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // === EDIT MODAL ===
        const editKategoriModal = (button) => {
            const id = button.dataset.id;
            const nama = button.dataset.nama;
            const url = "{{ route('kategori.update', ':id') }}".replace(':id', id);

            document.getElementById('edit_nama_kategori').value = nama;
            const form = document.getElementById('formKategoriModal');
            form.setAttribute('action', url);

            if (!form.querySelector('input[name="_method"]')) {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
                form.appendChild(methodInput);
            }

            document.getElementById('kategoriModal').classList.remove('hidden');
        };

        const kategoriModalClose = (button) => {
            const modalTarget = button.dataset.modalTarget;
            document.getElementById(modalTarget).classList.add('hidden');
        };

        // === TAMBAH MODAL (MOBILE) ===
        const tambahModalOpen = () => {
            document.getElementById('tambahModal').classList.remove('hidden');
        };
        const tambahModalClose = () => {
            document.getElementById('tambahModal').classList.add('hidden');
        };
    </script>
</x-app-layout>
