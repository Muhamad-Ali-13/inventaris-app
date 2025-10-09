{{-- resources/views/departemen/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Departemen') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- FORM INPUT -->
                <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">
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
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                required>
                        </div>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg px-5 py-2.5 transition">
                            Simpan
                        </button>
                    </form>
                </div>

                <!-- TABEL DATA -->
                <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">
                        Data Departemen
                    </h3>

                    <!-- Tabel untuk Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="bg-blue-50 text-gray-700 uppercase text-xs font-semibold">
                                <tr>
                                    <th class="px-6 py-3 text-center">No</th>
                                    <th class="px-6 py-3">Nama Departemen</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($departemen as $d)
                                    <tr class="border-t hover:bg-gray-50 transition">
                                        <td class="px-6 py-3 text-center font-medium text-gray-700">{{ $no++ }}</td>
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

                    <!-- Card Flutter-style untuk Mobile -->
                    <div class="space-y-4 md:hidden">
                        @php $no = 1; @endphp
                        @foreach ($departemen as $index => $d)
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 shadow-sm transform transition duration-500 ease-out opacity-0 translate-y-6 card-animate"
                                style="animation-delay: {{ $index * 0.1 }}s;">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="text-base font-semibold text-gray-800">
                                        {{ $no++ }}. {{ $d->nama_departemen }}
                                    </h4>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <button type="button"
                                        onclick="editDepartemenModal(this)"
                                        data-id="{{ $d->id }}"
                                        data-nama="{{ $d->nama_departemen }}"
                                        class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-2 rounded-md transition">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </button>

                                    <form action="{{ route('departemen.destroy', $d->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-md transition">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div id="departemenModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black bg-opacity-40" onclick="departemenModalClose(this)"></div>

        <div class="relative bg-white rounded-xl shadow-lg w-full max-w-md mx-5 p-6">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Edit Departemen</h3>
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
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        required>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="departemenModalClose(this)"
                        class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Animasi Flutter-style */
        @keyframes fadeUp {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-animate {
            animation: fadeUp 0.5s ease forwards;
        }
    </style>

    <script>
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
    </script>
</x-app-layout>
