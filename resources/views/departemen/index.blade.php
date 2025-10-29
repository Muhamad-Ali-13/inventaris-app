{{-- resources/views/departemen/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-700 leading-tight">
            {{ __('Manajemen Departemen') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gradient-to-br from-white via-green-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- FORM INPUT -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-green-100">
                    <h3 class="text-lg font-semibold mb-4 text-green-800 border-b pb-2">
                        Tambah Departemen
                    </h3>

                    <form action="{{ route('departemen.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="nama_departemen" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Departemen
                            </label>
                            <input type="text" name="nama_departemen" id="nama_departemen"
                                placeholder="Contoh: Keuangan"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                                required>
                        </div>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg px-5 py-2.5 transition">
                            Simpan
                        </button>
                    </form>
                </div>

                <!-- TABEL DATA -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-green-100">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-3">
                        <h3 class="text-lg font-semibold text-green-800 border-b pb-2 w-full md:w-auto">
                            Data Departemen
                        </h3>

                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-600">Show</label>
                                <select id="entries" class="border border-gray-300 rounded-lg px-2 py-1 text-gray-700">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>

                            <input type="text" id="searchInput" placeholder="Cari departemen..."
                                class="border border-gray-300 rounded-lg px-3 py-1.5 text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none w-full md:w-48">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600" id="departemenTable">
                            <thead class="bg-green-100 text-green-800 uppercase text-xs font-semibold">
                                <tr>
                                    <th class="px-6 py-3 text-center">No</th>
                                    <th class="px-6 py-3">Nama Departemen</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($departemen as $index => $d)
                                    <tr class="border-t hover:bg-green-50 transition">
                                        <td class="px-6 py-3 text-center font-medium text-gray-700">{{ $index + 1 }}</td>
                                        <td class="px-6 py-3">{{ $d->nama_departemen }}</td>
                                        <td class="px-6 py-3 flex justify-center gap-2">
                                            <button type="button"
                                                onclick="editDepartemenModal(this)"
                                                data-id="{{ $d->id }}"
                                                data-nama="{{ $d->nama_departemen }}"
                                                class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-2 rounded-md transition">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <form action="{{ route('departemen.destroy', $d->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-md transition">
                                                    <i class="fa-solid fa-trash"></i>
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
    <div id="departemenModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black bg-opacity-40" onclick="departemenModalClose(this)"></div>

        <div class="relative bg-white rounded-xl shadow-lg w-full max-w-md mx-5 p-6 border border-green-200">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-lg font-semibold text-green-700">Edit Departemen</h3>
                <button onclick="departemenModalClose(this)" class="text-gray-500 hover:text-gray-800 text-xl">
                    &times;
                </button>
            </div>

            <form method="POST" id="formDepartemenModal">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div class="mb-4">
                    <label for="edit_nama_departemen" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Departemen
                    </label>
                    <input type="text" name="nama_departemen" id="edit_nama_departemen"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                        required>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="departemenModalClose(this)"
                        class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal handler
        const editDepartemenModal = (button) => {
            const id = button.dataset.id;
            const nama = button.dataset.nama;
            const url = "{{ route('departemen.update', ':id') }}".replace(':id', id);

            document.getElementById('edit_nama_departemen').value = nama;
            document.getElementById('formDepartemenModal').setAttribute('action', url);
            document.getElementById('departemenModal').classList.remove('hidden');
        };

        const departemenModalClose = () => {
            document.getElementById('departemenModal').classList.add('hidden');
        };

        // Search & Entries
        const searchInput = document.getElementById('searchInput');
        const entriesSelect = document.getElementById('entries');
        const table = document.getElementById('departemenTable').getElementsByTagName('tbody')[0];
        const rows = Array.from(table.getElementsByTagName('tr'));

        function filterTable() {
            const query = searchInput.value.toLowerCase();
            const limit = parseInt(entriesSelect.value);
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const match = name.includes(query);
                if (match && visibleCount < limit) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('keyup', filterTable);
        entriesSelect.addEventListener('change', filterTable);

        // Initialize view
        filterTable();
    </script>
</x-app-layout>
