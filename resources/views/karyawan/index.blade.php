<x-app-layout>

    <div class="py-10 bg-gradient-to-br from-gray-50 via-white to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">

                <!-- HEADER + TOOLS -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">👥 Data Karyawan</h3>
                        <p class="text-gray-500 text-sm">Kelola seluruh data karyawan perusahaan dengan mudah.</p>
                    </div>
                    <button onclick="openModal('createModal')"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg shadow text-sm font-semibold">
                        + Tambah Karyawan
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
                                <option value="100">100</option>
                            </select>
                            entri
                        </label>
                    </div>

                    <!-- Search -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="searchInput" placeholder="Cari nama atau departemen..."
                            class="w-full border border-gray-300 rounded-lg pl-10 text-sm focus:ring-green-500 focus:border-green-500">
                        <i class="fi fi-rr-search absolute left-3 top-2.5 text-gray-400"></i>
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
                            @foreach ($karyawans as $index => $k)
                                <tr class="border-b hover:bg-green-50 transition">
                                    <td class="px-6 py-3">{{ $index + 1 }}</td>
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
                                            data-departemen="{{ $k->departemen_id }}" data-telp="{{ $k->no_telp }}"
                                            data-alamat="{{ $k->alamat }}" data-tanggal="{{ $k->tanggal_masuk }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 4h2m2 0h2a2 2 0 012 2v2m0 0v2m0-2h2m-2 0h-2m-2 0h-2m0 0V4m0 4H7m0 0H5m0 0H3m0 0V6a2 2 0 012-2h2m0 0h2m0 0v2" />
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
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD VIEW -->
                <div class="md:hidden space-y-4" id="karyawanCards">
                    @foreach ($karyawans as $k)
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 4h2m2 0h2a2 2 0 012 2v2m0 0v2m0-2h2m-2 0h-2m-2 0h-2m0 0V4m0 4H7m0 0H5m0 0H3m0 0V6a2 2 0 012-2h2m0 0h2m0 0v2" />
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
                    @endforeach
                </div>

            </div>
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

        function editKaryawanModal(button) {
            const id = button.dataset.id;
            const nip = button.dataset.nip;
            const nama = button.dataset.nama;
            const departemen = button.dataset.departemen;
            const telp = button.dataset.telp;
            const alamat = button.dataset.alamat;
            const tanggal = button.dataset.tanggal;

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


        // SEARCH, FILTER & ENTRIES
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.getElementById("searchInput");
            const departemenFilter = document.getElementById("departemenFilter");
            const entriesSelect = document.getElementById("entries");
            const table = document.getElementById("karyawanTable");
            const rows = Array.from(table.querySelectorAll("tbody tr"));

            function filterTable() {
                const search = searchInput.value.toLowerCase();
                const dep = departemenFilter.value.toLowerCase();
                const limit = parseInt(entriesSelect.value);

                let count = 0;
                rows.forEach(row => {
                    const nama = row.cells[2].innerText.toLowerCase();
                    const departemen = row.cells[3].innerText.toLowerCase();
                    if ((nama.includes(search) || departemen.includes(search)) && (dep === "" ||
                            departemen === dep) && count < limit) {
                        row.style.display = "";
                        count++;
                    } else {
                        row.style.display = "none";
                    }
                });
            }

            searchInput.addEventListener("input", filterTable);
            departemenFilter.addEventListener("change", filterTable);
            entriesSelect.addEventListener("change", filterTable);
            filterTable();
        });
    </script>
</x-app-layout>
