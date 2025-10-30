<x-app-layout>


    <div class="py-10 bg-gradient-to-br from-gray-50 via-white to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">

                <!-- HEADER + TOOLS -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">💼 Data Transaksi</h3>
                        <p class="text-gray-500 text-sm">Kelola dan pantau transaksi masuk & keluar secara real-time.</p>
                    </div>

                    @can('role-A')
                        <button onclick="openModal('createModal')"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg shadow transition text-sm font-semibold">
                            + Tambah Transaksi
                        </button>
                    @endcan
                </div>

                <!-- FILTERS + SEARCH + ENTRIES -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            Tampilkan
                            <select id="entries"
                                class="border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                                <option value="5" {{ request('entries') == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                            entri
                        </label>

                        <!-- Filter Status -->
                        <select id="statusFilter"
                            class="border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Semua Status</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="searchInput" placeholder="Cari nama user..."
                            class="w-full border border-gray-300 rounded-lg pl-10 text-sm focus:ring-green-500 focus:border-green-500">
                        <i class="fi fi-rr-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>

                <!-- DESKTOP TABLE -->
                <div class="overflow-x-auto rounded-lg border border-gray-100 hidden md:block">
                    <table class="w-full text-sm text-left text-gray-700" id="transaksiTable">
                        <thead class="bg-green-100 text-green-800 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">No</th>
                                <th class="px-6 py-3">Nama</th>
                                <th class="px-6 py-3">Departemen</th>
                                <th class="px-6 py-3">Jenis</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Tanggal Pengajuan</th>
                                <th class="px-6 py-3">Tanggal Disetujui</th>
                                <th class="px-6 py-3">Barang</th>
                                @can('role-A')
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksi as $index => $trx)
                                <tr class="border-b hover:bg-green-50 transition">
                                    <td class="px-6 py-3">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $trx->user->name ?? '-' }}</td>
                                    <td class="px-6 py-3">{{ $trx->departemen->nama_departemen ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $trx->jenis === 'pemasukan' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ ucfirst($trx->jenis) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if ($trx->status == 'approved') bg-green-100 text-green-700
                                            @elseif($trx->status == 'rejected') bg-red-100 text-red-700
                                            @else bg-yellow-100 text-yellow-700 @endif">
                                            {{ ucfirst($trx->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3">{{ $trx->tanggal_pengajuan }}</td>
                                    <td class="px-6 py-3">{{ $trx->tanggal_approval ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        @foreach ($trx->details as $d)
                                            <div class="text-gray-700">{{ $d->barang->nama_barang }}
                                                ({{ $d->jumlah }})
                                            </div>
                                        @endforeach
                                    </td>
                                    @can('role-A')
                                        <td class="px-4 py-2 text-center space-x-1">
                                            @if ($trx->status == 'pending')
                                                <form action="{{ route('transaksi.approve', $trx->id) }}" method="POST"
                                                    class="inline"> @csrf <button type="submit"
                                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                                                        Approve </button> </form>
                                                <form action="{{ route('transaksi.reject', $trx->id) }}" method="POST"
                                                    class="inline"> @csrf <button type="submit"
                                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                                                        Reject </button> </form>
                                                @endif @if ($trx->status !== 'approved')
                                                    <button onclick="openModal('editModal{{ $trx->id }}')"
                                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">
                                                        Edit </button>
                                                @else
                                                    <button
                                                        class="bg-gray-300 text-gray-500 px-3 py-1 rounded text-xs cursor-not-allowed"
                                                        disabled>Edit</button>
                                                @endif
                                                <form action="{{ route('transaksi.destroy', $trx->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin hapus transaksi ini?')" class="inline">
                                                    @csrf @method('DELETE') <button
                                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">Hapus</button>
                                                </form>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD VIEW -->
                <div class="md:hidden space-y-4" id="transaksiCards">
                    @foreach ($transaksi as $trx)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-lg text-gray-800">{{ $trx->user->name ?? '-' }}</h4>
                                <span
                                    class="text-xs px-2 py-1 rounded-full {{ $trx->jenis === 'pemasukan' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($trx->jenis) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>Departemen:</strong> {{ $trx->departemen->nama_departemen ?? '-' }}</p>
                                <p><strong>Status:</strong>
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if ($trx->status == 'approved') bg-green-100 text-green-700
                                        @elseif($trx->status == 'rejected') bg-red-100 text-red-700
                                        @else bg-yellow-100 text-yellow-700 @endif">
                                        {{ ucfirst($trx->status) }}
                                    </span>
                                </p>
                                <p><strong>Tanggal Pengajuan:</strong> {{ $trx->tanggal_pengajuan }}</p>
                                <p><strong>Tanggal Disetujui:</strong> {{ $trx->tanggal_approval ?? '-' }}</p>
                                <p><strong>Barang:</strong>
                                    @foreach ($trx->details as $d)
                                        <div>{{ $d->barang->nama_barang }} ({{ $d->jumlah }})</div>
                                    @endforeach
                                </p>
                                @can('role-A')
                                    <div class="flex justify-end space-x-1">
                                        <button onclick="openModal('editModal{{ $trx->id }}')"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">
                                            Edit </button>
                                        <form action="{{ route('transaksi.destroy', $trx->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin hapus transaksi ini?')" class="inline">
                                            @csrf @method('DELETE') <button
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">Hapus</button>
                                        </form>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 overflow-y-auto max-h-[90vh]">
            <form action="{{ route('transaksi.store') }}" method="POST"> @csrf <h3 class="text-xl font-bold mb-4">
                    Tambah Transaksi</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div> <label class="block mb-1">Jenis</label> <select name="jenis"
                            class="w-full border rounded p-2" required>
                            <option value="-">-</option> @can('role-A')
                                <option value="pemasukan">Pemasukan</option>
                            @endcan <option value="pengeluaran">Pengeluaran</option>
                        </select> </div>
                    <div> <label class="block mb-1">Tanggal Pengajuan</label> <input type="date"
                            name="tanggal_pengajuan" class="w-full border rounded p-2" required> </div>
                </div> <!-- Kategori -->
                <div class="mt-4"> <label class="block mb-2">Kategori</label> <select id="kategoriSelect"
                        class="border rounded p-2 w-full">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select> </div> <!-- Barang -->
                <div class="mt-4 hidden" id="barangSection"> <label class="block mb-2">Barang</label>
                    <div id="barang-wrapper">
                        <div class="flex gap-2 mb-2 items-center"> <select name="barang_id[]" id="barangSelect"
                                class="border rounded p-2 flex-1" required>
                                <option value="">-- Pilih Barang --</option>
                            </select> <input type="number" name="barang_jumlah[]" class="border rounded p-2 w-24"
                                placeholder="Jumlah" required> <button type="button"
                                class="bg-green-600 text-white px-3 py-1 rounded add-barang">+</button> <button
                                type="button"
                                class="bg-red-600 text-white px-3 py-1 rounded remove-barang">−</button> </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2"> <button type="button" class="px-4 py-2 border rounded"
                        onclick="closeModal('createModal')">Batal</button> <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded">Tambah</button> </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.getElementById("searchInput");
            const statusFilter = document.getElementById("statusFilter");
            const entriesSelect = document.getElementById("entries");
            const table = document.getElementById("transaksiTable");
            const rows = Array.from(table.querySelectorAll("tbody tr"));

            function filterTable() {
                const search = searchInput.value.toLowerCase();
                const status = statusFilter.value.toLowerCase();
                const limit = parseInt(entriesSelect.value);

                let count = 0;
                rows.forEach(row => {
                    const nama = row.cells[1].innerText.toLowerCase();
                    const st = row.cells[4].innerText.toLowerCase();
                    if (nama.includes(search) && (status === "" || st === status) && count < limit) {
                        row.style.display = "";
                        count++;
                    } else {
                        row.style.display = "none";
                    }
                });
            }

            searchInput.addEventListener("input", filterTable);
            statusFilter.addEventListener("change", filterTable);
            entriesSelect.addEventListener("change", filterTable);
            filterTable();
        });
    </script>
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        document.addEventListener("DOMContentLoaded", () => {
            // === FUNGSI UTAMA UNTUK CREATE (TAMBAH TRANSAKSI) ===
            const kategoriSelect = document.getElementById('kategoriSelect');
            const barangSection = document.getElementById('barangSection');
            const barangWrapper = document.getElementById('barang-wrapper');

            const setOptionsForAllBarangSelects = (items, wrapperSelector) => {
                document.querySelectorAll(`${wrapperSelector} select[name="barang_id[]"]`).forEach(select => {
                    select.innerHTML = '';

                    if (!items || items.length === 0) {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = 'Data barang kosong';
                        select.appendChild(opt);
                        select.disabled = true;
                    } else {
                        const placeholder = document.createElement('option');
                        placeholder.value = '';
                        placeholder.textContent = '-- Pilih Barang --';
                        select.appendChild(placeholder);

                        items.forEach(b => {
                            const opt = document.createElement('option');
                            opt.value = b.id;
                            opt.textContent = `${b.nama_barang} (Stok: ${b.stok})`;
                            opt.dataset.stok = b.stok;
                            if (b.stok <= 0) {
                                opt.disabled = true;
                                opt.classList.add('text-red-500');
                            }
                            select.appendChild(opt);
                        });

                        select.disabled = false;
                    }
                });
            };

            // === KHUSUS CREATE (TAMBAH) ===
            kategoriSelect?.addEventListener('change', function() {
                const kategoriId = this.value;
                if (!kategoriId) {
                    barangSection.classList.add('hidden');
                    document.querySelectorAll('#barang-wrapper select[name="barang_id[]"]').forEach(s => {
                        s.innerHTML = '<option value="">-- Pilih Barang --</option>';
                        s.disabled = true;
                    });
                    return;
                }

                barangSection.classList.remove('hidden');
                document.querySelectorAll('#barang-wrapper select[name="barang_id[]"]').forEach(s => {
                    s.innerHTML = '<option value="">Loading...</option>';
                    s.disabled = true;
                });

                fetch(`/barang/by-kategori/${kategoriId}`)
                    .then(res => res.json())
                    .then(data => setOptionsForAllBarangSelects(data, '#barang-wrapper'))
                    .catch(err => {
                        console.error('Gagal memuat barang:', err);
                    });
            });

            // === FUNGSI TAMBAH/HAPUS ROW BARANG SECARA DINAMIS (untuk semua modal) ===
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-barang')) {
                    const trxId = e.target.dataset.trx || ''; // bisa kosong untuk create
                    const wrapperSelector = trxId ? `#barang-wrapper-${trxId}` : '#barang-wrapper';
                    const row = e.target.closest('div.flex');
                    const clone = row.cloneNode(true);
                    const sel = clone.querySelector('select[name="barang_id[]"]');
                    const inp = clone.querySelector('input[name="barang_jumlah[]"]');
                    if (sel) sel.value = '';
                    if (inp) inp.value = '';
                    row.after(clone);
                    const currentOptions = document.querySelector(
                        `${wrapperSelector} select[name="barang_id[]"]`).innerHTML;
                    clone.querySelector('select[name="barang_id[]"]').innerHTML = currentOptions;
                }

                if (e.target.classList.contains('remove-barang')) {
                    const trxId = e.target.dataset.trx || '';
                    const wrapperSelector = trxId ? `#barang-wrapper-${trxId}` : '#barang-wrapper';
                    const allRows = document.querySelectorAll(`${wrapperSelector} div.flex`);
                    if (allRows.length > 1) {
                        e.target.closest('div.flex').remove();
                    } else {
                        alert('Minimal satu barang harus ada.');
                    }
                }
            });

            // === VALIDASI STOK INPUT BARANG UNTUK SEMUA MODAL (edit & create) ===
            document.addEventListener('input', function(e) {
                if (e.target.name === 'barang_jumlah[]') {
                    const jumlahInput = e.target;
                    const row = jumlahInput.closest('div.flex');
                    const selectBarang = row.querySelector('select[name="barang_id[]"]');
                    const selectedOption = selectBarang.options[selectBarang.selectedIndex];
                    const stokTersedia = selectedOption?.dataset?.stok ? parseInt(selectedOption.dataset
                        .stok) : null;
                    const jumlahInputValue = parseInt(jumlahInput.value);

                    // Ambil jenis transaksi dari kategori (pemasukan atau pengeluaran)
                    const kategoriSelect = document.getElementById('kategoriSelect');
                    const kategoriText = kategoriSelect?.options[kategoriSelect.selectedIndex]?.text
                        ?.toLowerCase();
                    const isPemasukan = kategoriText?.includes('masuk'); // true jika "pemasukan"

                    // 🔹 Jika pengeluaran, lakukan validasi stok
                    if (!isPemasukan && stokTersedia !== null && jumlahInputValue > stokTersedia) {
                        jumlahInput.value = stokTersedia;
                        alert(`⚠️ Jumlah melebihi stok tersedia (${stokTersedia}).`);
                    }

                    // 🔹 Jika pemasukan, tidak ada batas stok (boleh menambah meskipun stok 0)
                }
            });

            document.addEventListener('change', function(e) {
                if (e.target.name === 'barang_id[]') {
                    const row = e.target.closest('div.flex');
                    const jumlahInput = row.querySelector('input[name="barang_jumlah[]"]');
                    jumlahInput.value = '';
                }
            });

            // === KHUSUS UNTUK EDIT MODAL: FETCH DATA BARANG BERDASARKAN KATEGORI ===
            document.querySelectorAll('[id^="kategoriSelect"]').forEach(select => {
                select.addEventListener('change', function() {
                    const trxId = this.id.replace('kategoriSelect', '');
                    const wrapperSelector = `#barang-wrapper-${trxId}`;
                    const section = document.getElementById(`barangSection${trxId}`);
                    const kategoriId = this.value;

                    if (!kategoriId) {
                        section.classList.add('hidden');
                        document.querySelectorAll(`${wrapperSelector} select[name="barang_id[]"]`)
                            .forEach(s => {
                                s.innerHTML = '<option value="">-- Pilih Barang --</option>';
                                s.disabled = true;
                            });
                        return;
                    }

                    section.classList.remove('hidden');
                    document.querySelectorAll(`${wrapperSelector} select[name="barang_id[]"]`)
                        .forEach(s => {
                            s.innerHTML = '<option value="">Loading...</option>';
                            s.disabled = true;
                        });

                    fetch(`/barang/by-kategori/${kategoriId}`)
                        .then(res => res.json())
                        .then(data => setOptionsForAllBarangSelects(data, wrapperSelector))
                        .catch(err => {
                            console.error('Gagal memuat barang:', err);
                        });
                });
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 🔹 Tombol hapus dengan konfirmasi Swal (tidak diubah)
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    Swal.fire({
                        title: 'Yakin ingin menghapus data ini?',
                        text: "Data yang dihapus tidak dapat dikembalikan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${id}`).submit();
                        }
                    })
                });
            });
        });
    </script>

</x-app-layout>
