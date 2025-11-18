<x-app-layout>
    <div class="py-10 bg-gradient-to-br from-gray-50 via-white to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">

                <!-- HEADER -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="fi fi-rr-boxes text-green-600"></i>
                            Data Pemasukan Barang
                        </h3>
                        <p class="text-gray-500 text-sm">Kelola semua pemasukan barang ke dalam gudang.</p>
                    </div>

                    <button onclick="openModal('createModal')"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg shadow transition text-sm font-semibold flex items-center gap-2">
                        <i class="fi fi-rr-add-document"></i> Tambah Pemasukan
                    </button>
                </div>

                <!-- ALERT SWEETALERT -->
                @if (session('success'))
                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: '{{ session('success') }}',
                                showConfirmButton: false,
                                timer: 1800
                            });
                        });
                    </script>
                @endif
                @if (session('error'))
                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: '{{ session('error') }}',
                                showConfirmButton: true
                            });
                        });
                    </script>
                @endif

                <!-- TABLE -->
                <div class="overflow-x-auto border rounded-lg">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="bg-green-100 text-green-800 uppercase text-xs">
                            <tr>
                                <th class="px-3 py-2">No</th>
                                <th class="px-4 py-3">Kode Transaksi</th>
                                <th class="px-5 py-3">Nama</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-7 py-3">Tanggal Pengajuan</th>
                                <th class="px-8 py-3">Tanggal Disetujui</th>
                                <th class="px-11 py-3">Barang</th>
                                <th class="px-10 py-3">Total Harga</th>
                                <th class="px-9 py-3">Keterangan</th>
                                @if (auth()->user()->role == 'Admin')
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($transaksi as $index => $trx)
                                <tr class="border-b hover:bg-green-50 transition">
                                    <td class="px-3 py-2">{{ $index + 1 }}</td>

                                    <!-- KODE TRANSAKSI -->
                                    <td class="px-4 py-3 font-semibold text-green-700">{{ $trx->kode_transaksi }}</td>

                                    <!-- NAMA USER -->
                                    <td class="px-5 py-3">{{ $trx->user->name ?? '-' }}</td>

                                    <!-- STATUS -->
                                    <td class="px-6 py-3">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full
                            @if ($trx->status == 'approved') bg-green-100 text-green-700
                            @elseif($trx->status == 'rejected') bg-red-100 text-red-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                                            {{ ucfirst($trx->status) }}
                                        </span>
                                    </td>

                                    <!-- TANGGAL -->
                                    <td class="px-7 py-3">{{ $trx->tanggal_pengajuan }}</td>
                                    <td class="px-8 py-3">{{ $trx->tanggal_disetujui ?? '-' }}</td>

                                    <td class="px-11 py-3">
                                        @foreach ($trx->details as $d)
                                            @if ($d->barang)
                                                <div>{{ $d->barang->nama_barang }} ({{ $d->jumlah }} x Rp
                                                    {{ number_format($d->harga) }})</div>
                                            @else
                                                <div class="text-red-500">Barang sudah dihapus</div>
                                            @endif
                                        @endforeach
                                    </td>


                                    <!-- TOTAL HARGA -->
                                    <td class="px-10 py-3 font-semibold text-gray-800">
                                        Rp {{ number_format($trx->details->sum('total')) }}
                                    </td>

                                    <!-- KETERANGAN -->
                                    <td class="px-9 py-3">
                                        @if ($trx->status == 'pending')
                                            <span class="text-gray-400 text-xs">⏳ Menunggu proses</span>
                                        @elseif ($trx->status == 'approved')
                                            <span class="text-gray-400 text-xs">✅ Sudah diproses</span>
                                        @else
                                            <span class="text-gray-400 text-xs">❌ Ditolak</span>
                                        @endif
                                    </td>

                                    <!-- AKSI -->
                                    @if (auth()->user()->role == 'Admin')
                                        <td class="px-6 py-3 text-center space-x-2">

                                            <!-- APPROVE -->
                                            @if ($trx->status == 'pending')
                                                <form action="{{ route('pemasukan.approve', $trx->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menyetujui transaksi ini?')"
                                                    class="inline">
                                                    @csrf
                                                    <button title="Setujui" class="text-green-600 hover:text-green-800">
                                                        ✔
                                                    </button>
                                                </form>

                                                <!-- REJECT -->
                                                <form action="{{ route('pemasukan.reject', $trx->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menolak transaksi ini?')"class="inline">
                                                    @csrf
                                                    <button title="Tolak" class="text-red-600 hover:text-red-800">
                                                        ✖
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- ✏️ Tombol Edit (Hanya Muncul Jika Pending) --}}
                                            @if ($trx->status == 'pending')
                                                <button
                                                    onclick="openEditModal({{ $trx->id }},'{{ $trx->kode_transaksi }}','{{ $trx->tanggal_pengajuan }}')"
                                                    title="Edit" class="text-blue-600 hover:text-blue-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        class="w-5 h-5 inline">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                    </svg>
                                                </button>
                                            @endif

                                            {{-- ✅ Tombol Hapus Selalu Ada --}}
                                            <form action="{{ route('pemasukan.destroy', $trx->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus transaksi ini?')" class="inline">
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
                                    @endif
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-gray-500 py-4">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- MODAL TAMBAH -->
        <div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
                <form action="{{ route('pemasukan.store') }}" method="POST">
                    @csrf

                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-green-700">
                        <i class="fi fi-rr-add-document"></i> Tambah Data Pemasukan
                    </h3>

                    <!-- KODE TRANSAKSI (TAMPIL, TAPI TIDAK DIKIRIM) -->
                    <div class="mb-3">
                        <label class="block text-sm mb-1 font-medium">Kode Transaksi</label>
                        <input type="text" class="w-full border rounded p-2 bg-gray-100 font-semibold text-green-700"
                            value="{{ $kodeTransaksi }}" readonly>
                    </div>

                    <!-- TANGGAL -->
                    <div class="mb-3">
                        <label class="block text-sm mb-1 font-medium">Tanggal Pengajuan</label>
                        <input type="date" name="tanggal_pengajuan" class="w-full border rounded p-2" required>
                    </div>

                    <!-- KATEGORI -->
                    <div class="mb-3">
                        <label class="block text-sm mb-1 font-medium">Kategori</label>
                        <select name="kategori_id" id="kategoriSelect" class="w-full border rounded p-2" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategori as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- BARANG SESUAI KATEGORI -->
                    <div class="mb-3 hidden" id="barangSection">
                        <label class="block text-sm mb-1 font-medium">Barang</label>

                        <div id="barang-wrapper">
                            <div class="flex gap-2 mb-2 items-center barang-row">
                                <select name="barang_id[]" class="border rounded p-2 flex-1 barangSelect" required>
                                    <option value="">-- Pilih Barang --</option>
                                </select>

                                <input type="number" name="barang_jumlah[]" class="border rounded p-2 w-24"
                                    placeholder="Jumlah" min="1" required>

                                <button type="button"
                                    class="bg-green-600 text-white px-3 py-1 rounded add-barang">+</button>
                                <button type="button"
                                    class="bg-red-600 text-white px-3 py-1 rounded remove-barang">−</button>
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 border rounded">
                            Batal
                        </button>

                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6">
                <form id="editForm" action="{{ route('pemasukan.update', ':id:') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h3 class="text-xl font-bold mb-4 text-green-700 flex items-center gap-2">
                        ✏️ Edit Pemasukan
                    </h3>
                    {{-- <div>
                        <!-- Kode Transaksi (Tampil, tapi tidak dikirim) -->
                        <div class="mb-3">
                            <label class="block text-sm mb-1">Kode Transaksi</label>
                            <input type="text"
                                class="w-full border rounded p-2 bg-gray-100 font-semibold text-green-700"
                                value="{{ $kodeTransaksi }}" readonly>
                        </div>
                    </div> --}}
                    <!-- Tanggal -->
                    <div class="mb-3">
                        <label class="block text-sm mb-1">Tanggal Pengajuan</label>
                        <input type="date" name="tanggal_pengajuan" id="editTanggal"
                            class="w-full border rounded p-2" required>
                    </div>

                    <!-- Kategori -->
                    <div class="mb-3">
                        <label class="block text-sm mb-1">Kategori</label>
                        <select name="kategori_id" id="editKategori" class="w-full border rounded p-2" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategori as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Barang -->
                    <div id="editBarangSection" class="hidden mb-3">
                        <label class="block text-sm mb-1 font-medium">Barang</label>
                        <div id="editBarangWrapper"></div>
                    </div>

                    <!-- Action -->
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeModal('editModal')"
                            class="px-4 py-2 border rounded">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Update</button>
                    </div>
                </form>
            </div>
        </div>


        <script>
            const allBarang = @json($barang);

            // Event listener untuk perubahan kategori di modal tambah
            document.getElementById('kategoriSelect').addEventListener('change', function() {
                const kategoriId = this.value;
                const barangSection = document.getElementById('barangSection');
                const barangWrapper = document.getElementById('barang-wrapper');

                if (!kategoriId) {
                    barangSection.classList.add('hidden');
                    return;
                }

                // Fetch barang berdasarkan kategori
                fetch(`/barang/by-kategori/${kategoriId}`)
                    .then(res => res.json())
                    .then(barangs => {
                        barangSection.classList.remove('hidden');
                        barangWrapper.innerHTML = '';

                        // Buat baris barang baru
                        createBarangRowTambah(barangs);
                    });
            });

            // Fungsi helper untuk membuat baris barang di modal tambah
            function createBarangRowTambah(barangs) {
                const barangWrapper = document.getElementById('barang-wrapper');

                const row = document.createElement('div');
                row.classList.add('flex', 'gap-2', 'mb-2', 'items-center', 'barang-row');

                // Buat select untuk barang
                const select = document.createElement('select');
                select.name = 'barang_id[]';
                select.className = 'border rounded p-2 flex-1';
                select.required = true;

                // Tambahkan opsi default
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Pilih Barang --';
                select.appendChild(defaultOption);

                // Tambahkan opsi barang
                barangs.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.id;
                    opt.textContent = `${b.nama_barang} (Qty: ${b.qty})`;
                    select.appendChild(opt);
                });

                // Buat input untuk jumlah
                const input = document.createElement('input');
                input.type = 'number';
                input.name = 'barang_jumlah[]';
                input.className = 'border rounded p-2 w-24';
                input.min = '1';
                input.placeholder = 'Jumlah';
                input.required = true;

                // Buat tombol tambah/hapus
                const addBtn = document.createElement('button');
                addBtn.type = 'button';
                addBtn.className = 'bg-green-600 text-white px-3 py-1 rounded add-barang';
                addBtn.textContent = '+';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'bg-red-600 text-white px-3 py-1 rounded remove-barang';
                removeBtn.textContent = '−';

                row.appendChild(select);
                row.appendChild(input);
                row.appendChild(addBtn);
                row.appendChild(removeBtn);

                barangWrapper.appendChild(row);
            }

            // Event listener untuk tombol tambah/hapus di modal tambah
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-barang')) {
                    const row = e.target.closest('.barang-row');
                    const kategoriId = document.getElementById('kategoriSelect').value;

                    if (!kategoriId) return;

                    fetch(`/barang/by-kategori/${kategoriId}`)
                        .then(res => res.json())
                        .then(barangs => {
                            createBarangRowTambah(barangs);
                        });
                }

                if (e.target.classList.contains('remove-barang')) {
                    const rows = document.querySelectorAll('#barang-wrapper .barang-row');
                    if (rows.length > 1) {
                        e.target.closest('.barang-row').remove();
                    }
                }
            });
        </script>


        <script>
            function openModal(id) {
                document.getElementById(id).classList.remove('hidden');
            }

            function closeModal(id) {
                document.getElementById(id).classList.add('hidden');
            }

            function openEditModal(id, tanggal_pengajuan, kategori_id, details) {
                const form = document.getElementById('editForm');
                form.action = form.action.replace(':id:', id);

                document.getElementById('editTanggal').value = tanggal_pengajuan;

                // Set kategori jika ada
                if (kategori_id) {
                    document.getElementById('editKategori').value = kategori_id;
                    document.getElementById('editBarangSection').classList.remove('hidden');

                    // Muat data barang yang ada
                    const barangWrapper = document.getElementById('editBarangWrapper');
                    barangWrapper.innerHTML = '';

                    // Fetch barang berdasarkan kategori
                    fetch(`/barang/by-kategori/${kategori_id}`)
                        .then(res => res.json())
                        .then(barangs => {
                            // Jika ada details, tampilkan data yang ada
                            if (details && details.length > 0) {
                                details.forEach(d => {
                                    createBarangRow(barangs, d.kode_barang, d.jumlah);
                                });
                            } else {
                                // Jika tidak ada details, buat baris kosong
                                createBarangRow(barangs);
                            }
                        });
                } else {
                    // Jika tidak ada kategori_id, tampilkan form kosong
                    document.getElementById('editKategori').value = '';
                    document.getElementById('editBarangSection').classList.add('hidden');
                }

                openModal('editModal');
            }

            // Fungsi helper untuk membuat baris barang
            function createBarangRow(barangs, selectedBarangId = null, jumlah = null) {
                const barangWrapper = document.getElementById('editBarangWrapper');

                const row = document.createElement('div');
                row.classList.add('flex', 'gap-2', 'mb-2', 'items-center', 'barang-row');

                // Buat select untuk barang
                const select = document.createElement('select');
                select.name = 'barang_id[]';
                select.className = 'border rounded p-2 flex-1';
                select.required = true;

                // Tambahkan opsi default
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Pilih Barang --';
                select.appendChild(defaultOption);

                // Tambahkan opsi barang
                barangs.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.id;
                    opt.textContent = `${b.nama_barang} (Qty: ${b.qty})`;

                    // Pilih barang yang sesuai dengan data yang ada
                    if (selectedBarangId && b.id == selectedBarangId) {
                        opt.selected = true;
                    }

                    select.appendChild(opt);
                });

                // Buat input untuk jumlah
                const input = document.createElement('input');
                input.type = 'number';
                input.name = 'barang_jumlah[]';
                input.className = 'border rounded p-2 w-24';
                input.min = '1';
                input.required = true;

                // Isi jumlah jika ada
                if (jumlah !== null) {
                    input.value = jumlah;
                }

                // Buat tombol tambah/hapus
                const addBtn = document.createElement('button');
                addBtn.type = 'button';
                addBtn.className = 'bg-green-600 text-white px-3 py-1 rounded add-barang';
                addBtn.textContent = '+';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'bg-red-600 text-white px-3 py-1 rounded remove-barang';
                removeBtn.textContent = '−';

                row.appendChild(select);
                row.appendChild(input);
                row.appendChild(addBtn);
                row.appendChild(removeBtn);

                barangWrapper.appendChild(row);
            }

            // Event listener untuk perubahan kategori di modal edit
            document.getElementById('editKategori').addEventListener('change', function() {
                const kategoriId = this.value;
                const barangSection = document.getElementById('editBarangSection');
                const barangWrapper = document.getElementById('editBarangWrapper');

                if (!kategoriId) {
                    barangSection.classList.add('hidden');
                    return;
                }

                // Fetch barang berdasarkan kategori
                fetch(`/barang/by-kategori/${kategoriId}`)
                    .then(res => res.json())
                    .then(barangs => {
                        barangSection.classList.remove('hidden');
                        barangWrapper.innerHTML = '';

                        // Buat baris barang baru
                        createBarangRow(barangs);
                    });
            });

            // Event listener untuk tombol tambah/hapus di modal edit
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-barang')) {
                    const row = e.target.closest('.barang-row');
                    const kategoriId = document.getElementById('editKategori').value;

                    if (!kategoriId) return;

                    fetch(`/barang/by-kategori/${kategoriId}`)
                        .then(res => res.json())
                        .then(barangs => {
                            createBarangRow(barangs);
                        });
                }

                if (e.target.classList.contains('remove-barang')) {
                    const rows = document.querySelectorAll('#editBarangWrapper .barang-row');
                    if (rows.length > 1) {
                        e.target.closest('.barang-row').remove();
                    }
                }
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</x-app-layout>
