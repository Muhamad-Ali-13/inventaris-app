<x-app-layout>

    <!-- Area Notifikasi untuk Pesan Sukses/Error -->
    @if (session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2"
            id="notification">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-start space-x-2"
            id="notification">
            <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{!! session('error') !!}</span>
        </div>
    @endif

    <div class="py-10 bg-gradient-to-br from-gray-50 via-white to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">

                <!-- HEADER + TOOLS -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">👥 Data Karyawan</h3>
                        <p class="text-gray-500 text-sm">Kelola seluruh data karyawan perusahaan dengan mudah.</p>
                    </div>
                </div>

                <!-- FILTERS + SEARCH + ENTRIES -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            Tampilkan
                            <form action="{{ route('karyawans.index') }}" method="GET" class="inline-block">
                                @if (request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                <select name="entries" id="entries"
                                    class="border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500"
                                    onchange="this.form.submit()">
                                    <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100
                                    </option>
                                </select>
                                entri
                            </form>
                        </label>
                    </div>

                    <div class="relative w-full sm:w-64">
                        <form action="{{ route('karyawans.index') }}" method="GET" id="searchForm">
                            @if (request('entries'))
                                <input type="hidden" name="entries" value="{{ request('entries') }}">
                            @endif
                            <input type="text" name="search" placeholder="Cari nama atau departemen..."
                                value="{{ request('search') }}"
                                class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                            <i class="fi fi-rr-search absolute left-3 top-2.5 text-gray-400"></i>
                        </form>
                    </div>
                    <div class="flex gap-2">
                        <!-- Tombol Export -->
                        <a href="{{ route('karyawans.export') }}" title="Export Data"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2.5 rounded-lg shadow text-sm font-semibold inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export
                        </a>
                        <!-- Tombol Import -->
                        <button onclick="openImportModal()" title="Import Data"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg shadow text-sm font-semibold inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Import
                        </button>
                        <!-- Tombol Tambah -->
                        <button onclick="openModal('createModal')"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg shadow text-sm font-semibold">
                            + Tambah Karyawan
                        </button>
                    </div>
                </div>

                <!-- DESKTOP TABLE -->
                <div class="overflow-x-auto rounded-lg border border-gray-100 hidden md:block">
                    <table class="w-full text-sm text-left text-gray-700" id="karyawanTable">
                        <thead class="bg-green-100 text-green-800 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">No</th>
                                <th class="px-6 py-3">NIK</th>
                                <th class="px-6 py-3">Nama Lengkap</th>
                                <th class="px-6 py-3">Departemen</th>
                                <th class="px-6 py-3">No Telp</th>
                                <th class="px-6 py-3">Alamat</th>
                                <th class="px-6 py-3">Tanggal Masuk</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($karyawans as $k)
                                <tr class="border-b hover:bg-green-50 transition">
                                    <td class="px-6 py-3">{{ $karyawans->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-3">{{ $k->nip ?? '-' }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $k->nama_lengkap }}</td>
                                    <td class="px-6 py-3">{{ $k->departemen->nama_departemen ?? '-' }}</td>
                                    <td class="px-6 py-3">{{ $k->no_telp ?? '-' }}</td>
                                    <td class="px-6 py-3">{{ $k->alamat ?? '-' }}</td>
                                    <td class="px-6 py-3">{{ $k->tanggal_masuk ?? '-' }}</td>
                                    <td class="px-6 py-3 text-center flex justify-center gap-2">
                                        <button type="button" class="text-blue-600 hover:text-blue-800"
                                            onclick="editKaryawanModal(this)" data-id="{{ $k->id }}"
                                            data-nip="{{ $k->nip }}" data-nama="{{ $k->nama_lengkap }}"
                                            data-departemen="{{ $k->departemen_id }}"
                                            data-telp="{{ $k->no_telp }}" data-alamat="{{ $k->alamat }}"
                                            data-tanggal="{{ $k->tanggal_masuk }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                class="w-5 h-5 inline">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>

                                        <form action="{{ route('karyawans.destroy', $k->id) }}" method="POST"
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
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada data karyawan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD VIEW -->
                <div class="md:hidden space-y-4" id="karyawanCards">
                    @forelse ($karyawans as $k)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-lg text-gray-800">{{ $k->nama_lengkap }}</h4>
                                <span
                                    class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">{{ $k->departemen->nama_departemen ?? '-' }}</span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>NIP:</strong> {{ $k->nip ?? '-' }}</p>
                                <p><strong>No Telp:</strong> {{ $k->no_telp ?? '-' }}</p>
                                <p><strong>Alamat:</strong> {{ $k->alamat ?? '-' }}</p>
                                <p><strong>Tanggal Masuk:</strong> {{ $k->tanggal_masuk ?? '-' }}</p>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <button type="button" class="text-blue-600 hover:text-blue-800"
                                    onclick="editKaryawanModal(this)" data-id="{{ $k->id }}"
                                    data-nip="{{ $k->nip }}" data-nama="{{ $k->nama_lengkap }}"
                                    data-departemen="{{ $k->departemen_id }}" data-telp="{{ $k->no_telp }}"
                                    data-alamat="{{ $k->alamat }}" data-tanggal="{{ $k->tanggal_masuk }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5 inline">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>
                                <form action="{{ route('karyawans.destroy', $k->id) }}" method="POST"
                                    class="flex-1" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                    @empty
                        <div
                            class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 text-center text-gray-500">
                            Tidak ada data karyawan
                        </div>
                    @endforelse
                </div>
                <!-- PAGINATION -->
                <div class="mt-6 flex justify-center">
                    {{ $karyawans->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal dan script lainnya tidak berubah --}}
    {{-- 🌿 Modal Tambah Karyawan --}}
    <div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">
        <div
            class="bg-white rounded-2xl shadow-lg w-full max-w-4xl p-6 max-h-[90vh] overflow-y-auto border border-green-100">
            <div class="flex justify-between items-center mb-6 border-b pb-3">
                <h2 class="text-2xl font-bold text-green-700 flex items-center gap-2">
                    <i class="fi fi-rr-user-add"></i> Tambah Karyawan
                </h2>
            </div>

            <form action="{{ route('karyawans.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kolom kiri --}}
                    <div>
                        <h3 class="text-lg font-semibold text-green-700 mb-3">🧍 Data Karyawan</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">NIK</label>
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

    {{-- 🌿 Modal Import Karyawan --}}
    <div id="importModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-lg p-6 border border-blue-100">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h2 class="text-xl font-bold text-blue-700 flex items-center gap-2">
                    <i class="fi fi-rr-file-import"></i> Import Data Karyawan
                </h2>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <p class="text-xs text-blue-800">
                    <strong>Petunjuk:</strong> Pastikan file Excel memiliki kolom: <code>nama_lengkap</code>,
                    <code>email</code>, <code>role</code> (wajib). Kolom lainnya opsional. Download template untuk
                    format yang benar.
                </p>
            </div>

            <form action="{{ route('karyawans.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="import_file" class="block text-sm font-medium text-gray-700 mb-2">Pilih File
                        Excel</label>
                    <input type="file" name="file" id="import_file" accept=".xlsx, .xls, .csv" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('karyawans.export') }}" class="text-sm text-blue-600 hover:underline">Download
                        Template</a>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeImportModal()"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition text-sm">
                            Import
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="editModalContainer"></div> <!-- Container untuk modal edit dinamis -->

    <!-- SCRIPT -->
    <script>
        // MODAL TAMBAH
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // MODAL IMPORT
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        function editKaryawanModal(button) {
            const id = button.dataset.id;
            const nip = button.dataset.nip;
            const nama = button.dataset.nama;
            const departemen = button.dataset.departemen;
            const telp = button.dataset.telp;
            const alamat = button.dataset.alamat;
            const tanggal = button.dataset.tanggal;
            const role = button.dataset.role;

            let html = `
    <div id="editModal" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('editModal')"></div>
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative z-10">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Edit Karyawan</h3>
            </div>
            <form action="/karyawans/${id}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">NIK</label>
                    <input type="text" name="nip" value="${nip}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="${nama}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Departemen</label>
                    <select name="departemen_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                        @foreach ($departemen as $d)
                            <option value="{{ $d->id }}" ${departemen == {{ $d->id }} ? 'selected' : ''}>{{ $d->nama_departemen }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">No Telp</label>
                    <input type="text" name="no_telp" value="${telp}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
                    <textarea name="alamat" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">${alamat}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" value="${tanggal}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex justify-end gap-3 pt-3 border-t">
                    <button type="button" onclick="closeModal('editModal')" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium px-5 py-2 rounded-lg">Batal</button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-lg">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>`;
            document.getElementById('editModalContainer').innerHTML = html;
        }

        // Auto-hide notification
        window.addEventListener('load', () => {
            const notification = document.getElementById('notification');
            if (notification) {
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 5000); // Sembunyikan setelah 5 detik
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</x-app-layout>
