{{-- resources/views/karyawan/index.blade.php --}}
<x-app-layout>
    <div class="container mx-auto p-6">

        {{-- Header --}}
        <div
            class="flex flex-col md:flex-row justify-between items-center bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-green-700">📋 Data Karyawan</h1>
                <p class="text-gray-600 mt-1">Kelola seluruh data karyawan perusahaan dengan mudah.</p>
            </div>
            <button onclick="openModal('createModal')"
                class="mt-3 md:mt-0 bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-lg shadow-sm">
                + Tambah Karyawan
            </button>
        </div>

        {{-- Filter & Search --}}
        <div
            class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4 flex flex-col md:flex-row justify-between gap-3">
            <div class="flex items-center gap-2">
                <label for="entries" class="text-gray-700 font-medium">Tampilkan</label>
                <select id="entries" name="entries" class="border-gray-300 rounded-lg text-gray-700"
                    onchange="updateEntries(this.value)">
                    <option value="5" {{ request('entries') == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                </select>
                <span class="text-gray-700">entri</span>
            </div>

            <form method="GET" action="{{ route('karyawans.index') }}" class="flex gap-2">
                <input type="text" name="search" placeholder="Cari nama atau departemen..."
                    value="{{ request('search') }}"
                    class="w-64 border border-gray-300 rounded-lg px-3 py-2 text-gray-700 focus:ring-2 focus:ring-green-500 focus:outline-none">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Cari
                </button>
            </form>
        </div>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-green-100 text-green-800">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">NIP</th>
                        <th class="px-4 py-3">Nama Lengkap</th>
                        <th class="px-4 py-3">Departemen</th>
                        <th class="px-4 py-3">No Telp</th>
                        <th class="px-4 py-3">Alamat</th>
                        <th class="px-4 py-3">Tanggal Masuk</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($karyawans as $karyawan)
                        <tr class="border-t hover:bg-green-50 transition">
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3">{{ $karyawan->nip ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $karyawan->nama_lengkap }}</td>
                            <td class="px-4 py-3">{{ $karyawan->departemen->nama_departemen ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $karyawan->no_telp ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $karyawan->alamat ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $karyawan->tanggal_masuk ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <button onclick="openModal('editModal-{{ $karyawan->id }}')"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg shadow-sm">Edit</button>
                                    <form action="{{ route('karyawans.destroy', $karyawan->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg shadow-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-gray-500 py-4">Tidak ada data karyawan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-5">
            {{ $karyawans->appends(request()->query())->links() }}
        </div>
    </div>

    {{-- 🌿 Modal Tambah Karyawan --}}
    <div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">
        <div
            class="bg-white rounded-2xl shadow-lg w-full max-w-4xl p-6 max-h-[90vh] overflow-y-auto border border-green-100">
            <div class="flex justify-between items-center mb-6 border-b pb-3">
                <h2 class="text-2xl font-bold text-green-700 flex items-center gap-2">
                    <i class="fi fi-rr-user-add"></i> Tambah Karyawan
                </h2>
                <button onclick="closeModal('createModal')"
                    class="text-gray-500 hover:text-red-500 text-xl">&times;</button>
            </div>

            <form action="{{ route('karyawans.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kolom kiri --}}
                    <div>
                        <h3 class="text-lg font-semibold text-green-700 mb-3">🧍 Data Karyawan</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">NIP</label>
                                <input type="text" name="nip"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Nama Lengkap <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="nama_lengkap" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Departemen</label>
                                <select name="departemen_id"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                                    <option value="">-- Pilih Departemen --</option>
                                    @foreach ($departemen as $d)
                                        <option value="{{ $d->id }}">{{ $d->nama_departemen }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">No Telp</label>
                                <input type="text" name="no_telp"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
                                <textarea name="alamat"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    {{-- Kolom kanan --}}
                    <div>
                        <h3 class="text-lg font-semibold text-green-700 mb-3">🔐 Akun User</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Email <span
                                        class="text-red-500">*</span></label>
                                <input type="email" name="email" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Password <span
                                        class="text-red-500">*</span></label>
                                <input type="password" name="password" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="closeModal('createModal')"
                        class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white shadow-sm transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- 🌿 Modal Edit Karyawan --}}
    @foreach ($karyawans as $karyawan)
        <div id="editModal-{{ $karyawan->id }}"
            class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">
            <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6 border border-green-100">
                <div class="flex justify-between items-center mb-5 border-b pb-2">
                    <h2 class="text-xl font-bold text-green-700 flex items-center gap-2">
                        <i class="fi fi-rr-edit"></i> Edit Karyawan
                    </h2>
                    <button onclick="closeModal('editModal-{{ $karyawan->id }}')"
                        class="text-gray-500 hover:text-red-500 text-xl">&times;</button>
                </div>

                <form action="{{ route('karyawans.update', $karyawan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">NIP</label>
                            <input type="text" name="nip" value="{{ $karyawan->nip }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="{{ $karyawan->nama_lengkap }}" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Departemen</label>
                            <select name="departemen_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->id }}"
                                        {{ $d->id == $karyawan->departemen_id ? 'selected' : '' }}>
                                        {{ $d->nama_departemen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">No Telp</label>
                            <input type="text" name="no_telp" value="{{ $karyawan->no_telp }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
                            <textarea name="alamat"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">{{ $karyawan->alamat }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" value="{{ $karyawan->tanggal_masuk }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeModal('editModal-{{ $karyawan->id }}')"
                            class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white shadow-sm transition">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach


    {{-- Script Modal --}}
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
    </script>
</x-app-layout>
