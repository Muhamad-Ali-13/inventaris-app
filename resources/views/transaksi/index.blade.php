<x-app-layout>
    <div class="container mx-auto mt-8 px-4">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">💼 Manajemen Transaksi</h2>

        <!-- Filter & Entries -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <div class="flex items-center gap-2">
                <span class="text-gray-700">Show</span>
                <form method="GET" action="{{ route('transaksi.index') }}">
                    <select name="entries" onchange="this.form.submit()"
                        class="border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500">
                        <option value="5" {{ request('entries') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </form>
                <span class="text-gray-700">entries</span>
            </div>

            <!-- Filter Status -->
            <form method="GET" action="{{ route('transaksi.index') }}" class="flex items-center gap-2">
                <select name="status" class="border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Status</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition">
                    Filter
                </button>
            </form>

            <!-- Tombol Tambah -->
            <button onclick="openModal('createModal')"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg shadow-md transition duration-200">
                + Tambah Transaksi
            </button>
        </div>

        <!-- DESKTOP TABLE -->
        <div class="hidden md:block bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
            <table class="min-w-full text-sm">
                <thead class="bg-green-600 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">No</th>
                        <th class="px-4 py-2 text-left">Nama</th>
                        <th class="px-4 py-2 text-left">Departemen</th>
                        <th class="px-4 py-2 text-left">Jenis</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Tanggal Pengajuan</th>
                        <th class="px-4 py-2 text-left">Tanggal Disetujui</th>
                        <th class="px-4 py-2 text-left">Barang</th>
                        @if (auth()->user()->role === 'A')
                            <th class="px-4 py-2 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksi as $i => $trx)
                        <tr class="border-b hover:bg-green-50 transition">
                            <td class="px-4 py-2">{{ $i + $transaksi->firstItem() }}</td>
                            <td class="px-4 py-2">{{ $trx->user->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $trx->departemen->nama_departemen ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $trx->jenis === 'pemasukan' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($trx->jenis) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full
                                @if ($trx->status == 'approved') bg-green-100 text-green-700
                                @elseif($trx->status == 'rejected') bg-red-100 text-red-700
                                @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $trx->tanggal_pengajuan }}</td>
                            <td class="px-4 py-2">{{ $trx->tanggal_approval ?? '-' }}</td>
                            <td class="px-4 py-2">
                                @foreach ($trx->details as $d)
                                    <div class="text-gray-700">{{ $d->barang->nama_barang }} ({{ $d->jumlah }})
                                    </div>
                                @endforeach
                            </td>

                            @if (auth()->user()->role === 'A')
                                <td class="px-4 py-2 text-center space-x-1">
                                    @if ($trx->status == 'pending')
                                        <form action="{{ route('transaksi.approve', $trx->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('transaksi.reject', $trx->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                                                Reject
                                            </button>
                                        </form>
                                    @endif

                                    @if ($trx->status !== 'approved')
                                        <button onclick="openModal('editModal{{ $trx->id }}')"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">
                                            Edit
                                        </button>
                                    @else
                                        <button
                                            class="bg-gray-300 text-gray-500 px-3 py-1 rounded text-xs cursor-not-allowed"
                                            disabled>Edit</button>
                                    @endif

                                    <form action="{{ route('transaksi.destroy', $trx->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus transaksi ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">Hapus</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-gray-500">Belum ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $transaksi->links() }}
        </div>
    </div>



    <!-- Modal Tambah -->
    <div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 overflow-y-auto max-h-[90vh]">
            <form action="{{ route('transaksi.store') }}" method="POST">
                @csrf
                <h3 class="text-xl font-bold mb-4">Tambah Transaksi</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1">Jenis</label>
                        <select name="jenis" class="w-full border rounded p-2" required>
                            <option value="-">-</option>
                            @can('role-A')
                                <option value="pemasukan">Pemasukan</option>
                            @endcan
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1">Tanggal Pengajuan</label>
                        <input type="date" name="tanggal_pengajuan" class="w-full border rounded p-2" required>
                    </div>
                </div>

                <!-- Kategori -->
                <div class="mt-4">
                    <label class="block mb-2">Kategori</label>
                    <select id="kategoriSelect" class="border rounded p-2 w-full">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Barang -->
                <div class="mt-4 hidden" id="barangSection">
                    <label class="block mb-2">Barang</label>
                    <div id="barang-wrapper">
                        <div class="flex gap-2 mb-2 items-center">
                            <select name="barang_id[]" id="barangSelect" class="border rounded p-2 flex-1" required>
                                <option value="">-- Pilih Barang --</option>
                            </select>
                            <input type="number" name="barang_jumlah[]" class="border rounded p-2 w-24"
                                placeholder="Jumlah" required>
                            <button type="button"
                                class="bg-green-600 text-white px-3 py-1 rounded add-barang">+</button>
                            <button type="button"
                                class="bg-red-600 text-white px-3 py-1 rounded remove-barang">−</button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 border rounded"
                        onclick="closeModal('createModal')">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Tambah</button>
                </div>
            </form>
        </div>
    </div>

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

                    if (stokTersedia !== null && jumlahInputValue > stokTersedia) {
                        jumlahInput.value = stokTersedia;
                        alert(`⚠️ Jumlah melebihi stok tersedia (${stokTersedia}).`);
                    }
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
