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
                <div class="overflow-x-auto rounded-lg border border-gray-100 mt-4">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="bg-green-100 text-green-800 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">No</th>
                                <th class="px-6 py-3">User</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Tanggal Pengajuan</th>
                                <th class="px-6 py-3">Tanggal Approval</th>
                                <th class="px-6 py-3">Barang</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksi as $index => $trx)
                                <tr class="border-b hover:bg-green-50 transition">
                                    <td class="px-6 py-3">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3">{{ $trx->user->name ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if ($trx->status == 'approved') bg-green-100 text-green-700
                                            @elseif($trx->status == 'pending') bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-700 @endif">
                                            {{ ucfirst($trx->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3">{{ $trx->tanggal_pengajuan }}</td>
                                    <td class="px-6 py-3">{{ $trx->tanggal_approval ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        @foreach ($trx->details as $d)
                                            <div class="flex items-center gap-2">
                                                <i class="fi fi-rr-box text-gray-500"></i>
                                                {{ $d->barang->nama_barang }} ({{ $d->jumlah }})
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-3 text-center space-x-1">
                                        @if ($trx->status == 'pending')
                                            <form action="{{ route('pemasukan.approve', $trx->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs flex items-center gap-1">
                                                    <i class="fi fi-rr-check"></i> Approve
                                                </button>
                                            </form>

                                            <button onclick="openEditModal({{ $trx->id }})"
                                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs flex items-center gap-1">
                                                <i class="fi fi-rr-edit"></i> Edit
                                            </button>
                                        @endif

                                        <form action="{{ route('pemasukan.destroy', $trx->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin hapus data ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs flex items-center gap-1">
                                                <i class="fi fi-rr-trash"></i> Hapus
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

    <!-- MODAL TAMBAH -->
    <div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
            <form action="{{ route('pemasukan.store') }}" method="POST">
                @csrf
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-green-700">
                    <i class="fi fi-rr-add-document"></i> Tambah Data Pemasukan
                </h3>

                <div class="mb-3">
                    <label class="block text-sm mb-1 font-medium">Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_pengajuan" class="w-full border rounded p-2" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm mb-1 font-medium">Kategori</label>
                    <select id="kategoriSelect" class="w-full border rounded p-2" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 hidden" id="barangSection">
                    <label class="block text-sm mb-1 font-medium">Barang</label>
                    <div id="barang-wrapper">
                        <div class="flex gap-2 mb-2 items-center">
                            <select name="barang_id[]" class="border rounded p-2 flex-1" required>
                                <option value="">-- Pilih Barang --</option>
                            </select>
                            <input type="number" name="jumlah[]" class="border rounded p-2 w-24"
                                placeholder="Jumlah" required>
                            <button type="button" class="bg-green-600 text-white px-3 py-1 rounded add-barang">+</button>
                            <button type="button" class="bg-red-600 text-white px-3 py-1 rounded remove-barang">−</button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModal('createModal')"
                        class="px-4 py-2 border rounded">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6">
            <form id="editForm" method="POST">@csrf @method('PUT')
                <h3 class="text-xl font-bold mb-4 text-green-700 flex items-center gap-2">
                    ✏️ Edit Pemasukan
                </h3>

                <div class="mb-3">
                    <label class="block text-sm mb-1">Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_pengajuan" id="editTanggal"
                        class="w-full border rounded p-2" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm mb-1">Kategori</label>
                    <select name="kategori_id" id="editKategori" class="w-full border rounded p-2" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="editBarangSection" class="hidden">
                    <label class="block text-sm mb-1 font-medium">Barang</label>
                    <div id="editBarangWrapper"></div>
                </div>

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
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

        // === PILIH KATEGORI → MUNCUL BARANG ===
        document.getElementById('kategoriSelect').addEventListener('change', function() {
            const id = this.value;
            const section = document.getElementById('barangSection');
            const select = document.querySelector('#barang-wrapper select');

            if (!id) { section.classList.add('hidden'); return; }

            fetch(`/barang/by-kategori/${id}`)
                .then(res => res.json())
                .then(data => {
                    section.classList.remove('hidden');
                    select.innerHTML = '<option value="">-- Pilih Barang --</option>';
                    data.forEach(b => {
                        const opt = document.createElement('option');
                        opt.value = b.id;
                        opt.textContent = `${b.nama_barang} (Stok: ${b.stok})`;
                        select.appendChild(opt);
                    });
                });
        });

        // === TAMBAH & HAPUS BARANG ===
        document.addEventListener('click', e => {
            if (e.target.classList.contains('add-barang')) {
                const row = e.target.closest('div.flex');
                const clone = row.cloneNode(true);
                clone.querySelector('select').value = '';
                clone.querySelector('input').value = '';
                row.after(clone);
            }
            if (e.target.classList.contains('remove-barang')) {
                const all = document.querySelectorAll('#barang-wrapper div.flex');
                if (all.length > 1) e.target.closest('div.flex').remove();
            }
        });

        // === FUNGSI OPEN EDIT ===
        function openEditModal(id) {
            fetch(`/pemasukan/${id}/edit`)
                .then(r => r.json())
                .then(data => {
                    const form = document.getElementById('editForm');
                    form.action = `/pemasukan/${id}`;
                    document.getElementById('editTanggal').value = data.tanggal_pengajuan;
                    document.getElementById('editKategori').value = data.kategori_id;
                    const wrapper = document.getElementById('editBarangWrapper');
                    wrapper.innerHTML = '';

                    data.details.forEach(d => {
                        const row = document.createElement('div');
                        row.className = 'flex gap-2 mb-2 items-center';

                        const select = document.createElement('select');
                        select.name = 'barang_id[]';
                        select.className = 'border rounded p-2 flex-1';
                        data.barang.forEach(b => {
                            const opt = document.createElement('option');
                            opt.value = b.id;
                            opt.textContent = `${b.nama_barang} (Stok: ${b.stok})`;
                            if (b.id == d.barang_id) opt.selected = true;
                            select.appendChild(opt);
                        });

                        const input = document.createElement('input');
                        input.type = 'number';
                        input.name = 'jumlah[]';
                        input.value = d.jumlah;
                        input.className = 'border rounded p-2 w-24';

                        row.append(select, input);
                        wrapper.appendChild(row);
                    });

                    document.getElementById('editBarangSection').classList.remove('hidden');
                    openModal('editModal');
                });
        }
    </script>
</x-app-layout>
