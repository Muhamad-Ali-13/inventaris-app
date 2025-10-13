<x-app-layout>
    <div class="container mx-auto mt-6 px-4">
        <h2 class="text-2xl font-bold mb-4">Manajemen Transaksi</h2>

        <!-- Tombol Tambah -->
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4"
            onclick="openModal('createModal')">
            Tambah Transaksi
        </button>

        <!-- DESKTOP TABLE -->
        <div class="overflow-x-auto hidden md:block">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">No</th>
                        <th class="px-4 py-2 border">Nama</th>
                        <th class="px-4 py-2 border">Departemen</th>
                        <th class="px-4 py-2 border">Tipe</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Tanggal Pengajuan</th>
                        <th class="px-4 py-2 border">Tanggal Disetujui</th>
                        <th class="px-4 py-2 border">Barang</th>
                        @if (auth()->user()->role === 'A')
                            <th class="px-4 py-2 border">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksi as $i => $trx)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $i + $transaksi->firstItem() }}</td>
                            <td class="px-4 py-2 border">{{ $trx->user->name ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $trx->departemen->nama_departemen ?? '-' }}</td>
                            <td class="px-4 py-2 border">
                                <span
                                    class="px-2 py-1 rounded text-white {{ $trx->tipe === 'pemasukan' ? 'bg-green-500' : 'bg-red-500' }}">
                                    {{ ucfirst($trx->tipe) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border">
                                <span
                                    class="px-2 py-1 rounded text-white
                                    {{ $trx->status == 'approved' ? 'bg-green-500' : ($trx->status == 'rejected' ? 'bg-red-500' : 'bg-yellow-500') }}">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border">{{ $trx->tanggal_pengajuan }}</td>
                            <td class="px-4 py-2 border">{{ $trx->tanggal_approval ?? '-' }}</td>
                            <td class="px-4 py-2 border">
                                @foreach ($trx->details as $d)
                                    {{ $d->barang->nama_barang }} ({{ $d->jumlah }})<br>
                                @endforeach
                            </td>
                            @if (auth()->user()->role === 'A')
                                <td class="px-4 py-2 border text-center">
                                    @if ($trx->status == 'pending')
                                        <form action="{{ route('transaksi.approve', $trx->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-green-600 text-white px-2 py-1 rounded text-xs">Approve</button>
                                        </form>
                                        <form action="{{ route('transaksi.reject', $trx->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-red-600 text-white px-2 py-1 rounded text-xs">Reject</button>
                                        </form>
                                    @endif
                                    <button class="bg-yellow-500 text-white px-2 py-1 rounded text-xs"
                                        onclick="openModal('editModal{{ $trx->id }}')">Edit</button>
                                    <form action="{{ route('transaksi.destroy', $trx->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-600 text-white px-2 py-1 rounded text-xs">Hapus</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">Belum ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MOBILE CARD -->
        <div class="md:hidden space-y-4">
            @forelse ($transaksi as $i => $trx)
                <div class="bg-white shadow rounded-lg p-4 border">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold text-lg">{{ $trx->user->name ?? '-' }}</h3>
                        <span
                            class="text-xs px-2 py-1 rounded text-white {{ $trx->tipe === 'pemasukan' ? 'bg-green-500' : 'bg-red-500' }}">
                            {{ ucfirst($trx->tipe) }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-600"><strong>Departemen:</strong>
                        {{ $trx->departemen->nama_departemen ?? '-' }}
                    </p>
                    <p class="text-sm text-gray-600"><strong>Status:</strong>
                        <span
                            class="px-2 py-1 rounded text-white {{ $trx->status == 'approved' ? 'bg-green-500' : ($trx->status == 'rejected' ? 'bg-red-500' : 'bg-yellow-500') }}">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </p>
                    <p class="text-sm text-gray-600"><strong>Tgl Pengajuan:</strong> {{ $trx->tanggal_pengajuan }}</p>
                    <p class="text-sm text-gray-600"><strong>Tgl Disetujui:</strong>
                        {{ $trx->tanggal_approval ?? '-' }}
                    </p>

                    <div class="mt-2">
                        <strong>Barang:</strong>
                        <ul class="list-disc list-inside text-sm text-gray-700">
                            @foreach ($trx->details as $d)
                                <li>{{ $d->barang->nama_barang }} ({{ $d->jumlah }})</li>
                            @endforeach
                        </ul>
                    </div>

                    @if (auth()->user()->role === 'A')
                        <div class="flex gap-2 mt-3">
                            @if ($trx->status == 'pending')
                                <form action="{{ route('transaksi.approve', $trx->id) }}" method="POST"
                                    class="flex-1">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-green-600 text-white px-2 py-1 rounded text-xs">Approve</button>
                                </form>
                                <form action="{{ route('transaksi.reject', $trx->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-red-600 text-white px-2 py-1 rounded text-xs">Reject</button>
                                </form>
                            @endif
                            <button onclick="openModal('editModal{{ $trx->id }}')"
                                class="flex-1 bg-yellow-500 text-white px-2 py-1 rounded text-xs">Edit</button>
                            <form action="{{ route('transaksi.destroy', $trx->id) }}" method="POST"
                                onsubmit="return confirm('Yakin hapus transaksi ini?')" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button class="w-full bg-red-600 text-white px-2 py-1 rounded text-xs">Hapus</button>
                            </form>
                        </div>
                    @endif
                </div>


                <!-- Modal Edit (Per Transaksi) -->
                <div id="editModal{{ $trx->id }}"
                    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 overflow-y-auto max-h-[90vh]">
                        <form action="{{ route('transaksi.update', $trx->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <h3 class="text-xl font-bold mb-4">Edit Transaksi</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Tipe -->
                                <div>
                                    <label class="block mb-1">Tipe</label>
                                    <select name="tipe" class="w-full border rounded p-2" required>
                                        <option value="pemasukan" {{ $trx->tipe == 'pemasukan' ? 'selected' : '' }}>
                                            Pemasukan
                                        </option>
                                        <option value="pengeluaran"
                                            {{ $trx->tipe == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran
                                        </option>
                                        <option value="permintaan" {{ $trx->tipe == 'permintaan' ? 'selected' : '' }}>
                                            Permintaan
                                        </option>
                                    </select>
                                </div>

                                <!-- Tanggal -->
                                <div>
                                    <label class="block mb-1">Tanggal Pengajuan</label>
                                    <input type="date" name="tanggal_pengajuan" class="w-full border rounded p-2"
                                        value="{{ $trx->tanggal_pengajuan }}" required>
                                </div>

                                <!-- Kategori -->
                                <div class="mt-4 col-span-2">
                                    <label class="block mb-2">Kategori</label>
                                    <select id="kategoriSelect{{ $trx->id }}" class="border rounded p-2 w-full">
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach ($kategori as $k)
                                            <option value="{{ $k->id }}"
                                                {{ $trx->details->first()?->barang?->kategori_id == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Barang -->
                                <div class="mt-4 col-span-2" id="barangSection{{ $trx->id }}">
                                    <label class="block mb-2">Barang</label>
                                    <div id="barang-wrapper-{{ $trx->id }}">
                                        @foreach ($trx->details as $detail)
                                            <div class="flex gap-2 mb-2 items-center">
                                                <select name="barang_id[]" class="border rounded p-2 flex-1" required>
                                                    <option value="">-- Pilih Barang --</option>
                                                    @foreach ($barang as $b)
                                                        @if ($b->kategori_id == $detail->barang->kategori_id)
                                                            <option value="{{ $b->id }}"
                                                                {{ $detail->barang_id == $b->id ? 'selected' : '' }}>
                                                                {{ $b->nama_barang }} (Stok :
                                                                {{ $b->stok }})
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <input type="number" name="barang_jumlah[]"
                                                    class="border rounded p-2 w-24" placeholder="Jumlah"
                                                    value="{{ $detail->jumlah }}" required>
                                                <button type="button"
                                                    class="bg-green-600 text-white px-3 py-1 rounded add-barang"
                                                    data-trx="{{ $trx->id }}">+</button>
                                                <button type="button"
                                                    class="bg-red-600 text-white px-3 py-1 rounded remove-barang"
                                                    data-trx="{{ $trx->id }}">−</button>
                                            </div>
                                        @endforeach
                                        @if ($trx->details->isEmpty())
                                            <!-- Jika belum ada detail -->
                                            <div class="flex gap-2 mb-2 items-center">
                                                <select name="barang_id[]" class="border rounded p-2 flex-1" required>
                                                    <option value="">-- Pilih Barang --</option>
                                                </select>
                                                <input type="number" name="barang_jumlah[]"
                                                    class="border rounded p-2 w-24" placeholder="Jumlah" required>
                                                <button type="button"
                                                    class="bg-green-600 text-white px-3 py-1 rounded add-barang"
                                                    data-trx="{{ $trx->id }}">+</button>
                                                <button type="button"
                                                    class="bg-red-600 text-white px-3 py-1 rounded remove-barang"
                                                    data-trx="{{ $trx->id }}">−</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-2">
                                <button type="button" class="px-4 py-2 border rounded"
                                    onclick="closeModal('editModal{{ $trx->id }}')">Batal</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4">Belum ada transaksi</td>
                </tr>
            @endforelse
            </tbody>
            </table>
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
                        <label class="block mb-1">Tipe</label>
                        <select name="tipe" class="w-full border rounded p-2" required>
                            <option value="-">-</option>
                            <option value="pemasukan">Pemasukan</option>
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
            const kategoriSelect = document.getElementById('kategoriSelect');
            const barangSection = document.getElementById('barangSection');
            const barangWrapper = document.getElementById('barang-wrapper');

            const setOptionsForAllBarangSelects = (items) => {
                document.querySelectorAll('#barang-wrapper select[name="barang_id[]"]').forEach(select => {
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

                            // tampilkan stok
                            if (b.stok <= 0) {
                                opt.textContent = `${b.nama_barang} (Stok Habis)`;
                                opt.disabled = true; // tidak bisa dipilih
                                opt.classList.add('text-red-500'); // styling opsional
                            } else {
                                opt.textContent = `${b.nama_barang} (Stok: ${b.stok})`;
                            }

                            select.appendChild(opt);
                        });

                        select.disabled = false;
                    }
                });
            };


            // fetch and fill barang when kategori changes
            kategoriSelect?.addEventListener('change', function() {
                const kategoriId = this.value;

                // hide section if no kategori
                if (!kategoriId) {
                    barangSection.classList.add('hidden');
                    // reset selects
                    document.querySelectorAll('#barang-wrapper select[name="barang_id[]"]').forEach(s => {
                        s.innerHTML = '<option value="">-- Pilih Barang --</option>';
                        s.disabled = true;
                    });
                    return;
                }

                // show section and fetch
                barangSection.classList.remove('hidden');
                // set temporary loading state
                document.querySelectorAll('#barang-wrapper select[name="barang_id[]"]').forEach(s => {
                    s.innerHTML = '<option value="">Loading...</option>';
                    s.disabled = true;
                });

                fetch(`/barang/by-kategori/${kategoriId}`)
                    .then(res => res.json())
                    .then(data => {
                        console.log('Data barang:', data); // 👈 lihat field apa yg dikirim
                        setOptionsForAllBarangSelects(data);
                    })
                    .catch(err => {
                        console.error('Gagal memuat barang:', err);
                        // show gagal
                        document.querySelectorAll('#barang-wrapper select[name="barang_id[]"]').forEach(
                            s => {
                                s.innerHTML = '<option value="">Gagal memuat</option>';
                                s.disabled = true;
                            });
                    });
            });

            // dynamic add / remove rows
            barangWrapper.addEventListener('click', function(e) {
                // add
                if (e.target.classList.contains('add-barang')) {
                    const row = e.target.closest('div.flex');
                    const clone = row.cloneNode(true);
                    // Reset values in clone
                    const sel = clone.querySelector('select[name="barang_id[]"]');
                    const inp = clone.querySelector('input[name="barang_jumlah[]"]');
                    if (sel) sel.value = '';
                    if (inp) inp.value = '';
                    row.after(clone);

                    // ensure cloned select has same options as current selects
                    const currentOptions = document.querySelector(
                        '#barang-wrapper select[name="barang_id[]"]').innerHTML;
                    clone.querySelector('select[name="barang_id[]"]').innerHTML = currentOptions;
                    // ensure remove button visible (if present)
                    const removeBtn = clone.querySelector('.remove-barang');
                    if (removeBtn) removeBtn.classList.remove('hidden');
                }

                // remove
                if (e.target.classList.contains('remove-barang')) {
                    const allRows = barangWrapper.querySelectorAll('div.flex');
                    if (allRows.length > 1) {
                        e.target.closest('div.flex').remove();
                    } else {
                        alert('Minimal satu barang harus ada.');
                    }
                }
            });

            // Disable barangSelect initially (until kategori dipilih)
            document.querySelectorAll('#barang-wrapper select[name="barang_id[]"]').forEach(s => {
                s.innerHTML = '<option value="">-- Pilih Kategori dulu --</option>';
                s.disabled = true;
            });
        });
    </script>
</x-app-layout>
