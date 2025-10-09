{{-- resources/views/karyawan/index.blade.php --}}
<x-app-layout>
    <div class="container mx-auto p-4">

        {{-- Alert Success --}}
        @if (session('success'))
            <div class="bg-green-500 text-white p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Judul --}}
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Data Karyawan</h1>

            {{-- Button Tambah (desktop) --}}
            <button onclick="openModal('createModal')"
                class="hidden md:inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Tambah Karyawan
            </button>
        </div>

        {{-- Tabel Desktop --}}
        <div class="hidden md:block overflow-x-auto bg-white shadow rounded">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">NIP</th>
                        <th class="px-4 py-2">Nama Lengkap</th>
                        <th class="px-4 py-2">Departemen</th>
                        <th class="px-4 py-2">No Telp</th>
                        <th class="px-4 py-2">Alamat</th>
                        <th class="px-4 py-2">Tanggal Masuk</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($karyawans as $karyawan)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2">{{ $karyawan->nip ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $karyawan->nama_lengkap }}</td>
                            <td class="px-4 py-2">{{ $karyawan->departemen->nama_departemen ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $karyawan->no_telp ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $karyawan->alamat ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $karyawan->tanggal_masuk ?? '-' }}</td>
                            <td class="px-4 py-2 flex gap-2">
                                <button onclick="openModal('editModal-{{ $karyawan->id }}')"
                                    class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
                                <form action="{{ route('karyawans.destroy', $karyawan->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin hapus karyawan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile View: Card List --}}
        <div class="md:hidden space-y-4 mt-4">
            @foreach ($karyawans as $karyawan)
                <div class="bg-white shadow rounded p-4">
                    <h2 class="font-semibold text-lg">{{ $karyawan->nama_lengkap }}</h2>
                    <p><strong>NIP:</strong> {{ $karyawan->nip ?? '-' }}</p>
                    <p><strong>Departemen:</strong> {{ $karyawan->departemen->nama_departemen ?? '-' }}</p>
                    <p><strong>No Telp:</strong> {{ $karyawan->no_telp ?? '-' }}</p>
                    <p><strong>Alamat:</strong> {{ $karyawan->alamat ?? '-' }}</p>
                    <p><strong>Tanggal Masuk:</strong> {{ $karyawan->tanggal_masuk ?? '-' }}</p>

                    <div class="flex gap-2 mt-3">
                        <button onclick="openModal('editModal-{{ $karyawan->id }}')"
                            class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
                        <form action="{{ route('karyawans.destroy', $karyawan->id) }}" method="POST"
                            onsubmit="return confirm('Yakin hapus karyawan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Floating Button (mobile only) --}}
        <button onclick="openModal('createModal')"
            class="md:hidden fixed bottom-6 right-6 bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700">
            +
        </button>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $karyawans->links() }}
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white rounded shadow-lg w-full max-w-4xl p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Tambah Karyawan</h2>
            <form action="{{ route('karyawans.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kolom kiri: Data Karyawan --}}
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Data Karyawan</h3>
                        <div class="mb-3">
                            <label class="block">NIP</label>
                            <input type="text" name="nip" class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mb-3">
                            <label class="block">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="mb-3">
                            <label class="block">Departemen</label>
                            <select name="departemen_id" class="w-full border rounded px-3 py-2">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block">No Telp</label>
                            <input type="text" name="no_telp" class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mb-3">
                            <label class="block">Alamat</label>
                            <textarea name="alamat" class="w-full border rounded px-3 py-2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="block">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="w-full border rounded px-3 py-2">
                        </div>
                    </div>

                    {{-- Kolom kanan: Data User --}}
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Akun User</h3>
                        <div class="mb-3">
                            <label class="block">Email</label>
                            <input type="email" name="email" class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="mb-3">
                            <label class="block">Password</label>
                            <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
                        </div>
                        {{-- <div class="mb-3">
                            <label class="block">Role</label>
                            <select name="role" class="w-full border rounded px-3 py-2">
                                <option value="karyawan">Karyawan</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div> --}}
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModal('createModal')"
                        class="px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>



    {{-- Modal Edit untuk setiap karyawan --}}
    @foreach ($karyawans as $karyawan)
        <div id="editModal-{{ $karyawan->id }}"
            class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
            <div class="bg-white rounded shadow-lg w-full max-w-md p-6">
                <h2 class="text-xl font-bold mb-4">Edit Karyawan</h2>
                <form action="{{ route('karyawans.update', $karyawan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="block">NIP</label>
                        <input type="text" name="nip" value="{{ $karyawan->nip }}"
                            class="w-full border rounded px-3 py-2">
                    </div>
                    <div class="mb-3">
                        <label class="block">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ $karyawan->nama_lengkap }}"
                            class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="block">Departemen</label>
                        <select name="departemen_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->id }}"
                                    {{ $d->id == $karyawan->departemen_id ? 'selected' : '' }}>
                                    {{ $d->nama_departemen }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block">No Telp</label>
                        <input type="text" name="no_telp" value="{{ $karyawan->no_telp }}"
                            class="w-full border rounded px-3 py-2">
                    </div>
                    <div class="mb-3">
                        <label class="block">Alamat</label>
                        <textarea name="alamat" class="w-full border rounded px-3 py-2">{{ $karyawan->alamat }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="block">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" value="{{ $karyawan->tanggal_masuk }}"
                            class="w-full border rounded px-3 py-2">
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="closeModal('editModal-{{ $karyawan->id }}')"
                            class="px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
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
