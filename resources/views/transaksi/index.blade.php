<x-app-layout>
    <div class="container mx-auto mt-6 px-4">
        <h2 class="text-2xl font-bold mb-4">Manajemen Transaksi</h2>

        <!-- Tombol Tambah -->
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4" 
                onclick="openModal('createModal')">
            Tambah Transaksi
        </button>

        <!-- Tabel Transaksi -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">No</th>
                        <th class="px-4 py-2 border">Nama</th>
                        <th class="px-4 py-2 border">Departemen</th>
                        <th class="px-4 py-2 border">Tipe</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Tanggal Pengajuan</th>
                        <th class="px-4 py-2 border">Tanggal Approval</th>
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
                                <span class="px-2 py-1 rounded text-white {{ $trx->tipe === 'pemasukan' ? 'bg-green-500' : 'bg-red-500' }}">
                                    {{ ucfirst($trx->tipe) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border">
                                <span class="px-2 py-1 rounded text-white
                                    {{ $trx->status == 'approved' ? 'bg-green-500' : ($trx->status == 'rejected' ? 'bg-red-500' : 'bg-yellow-500') }}">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border">{{ $trx->tanggal_pengajuan }}</td>
                            <td class="px-4 py-2 border">{{ $trx->tanggal_approval ?? '-' }}</td>
                            <td class="px-4 py-2 border">
                                <ul class="list-disc list-inside">
                                    @foreach ($trx->details as $d)
                                        <li>{{ $d->barang->nama_barang }} ({{ $d->jumlah }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            @if (auth()->user()->role === 'A')
                            <td class="px-4 py-2 border flex gap-2 justify-center">
                                @if ($trx->status == 'pending')
                                    <form action="{{ route('transaksi.approve', $trx->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded text-xs">Approve</button>
                                    </form>
                                    <form action="{{ route('transaksi.reject', $trx->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded text-xs">Reject</button>
                                    </form>
                                @endif
                                <button class="bg-yellow-500 text-white px-2 py-1 rounded text-xs" 
                                        onclick="openModal('editModal{{ $trx->id }}')">Edit</button>
                                <form action="{{ route('transaksi.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 text-white px-2 py-1 rounded text-xs">Hapus</button>
                                </form>
                            </td>
                            @endif
                        </tr>

                        <!-- Modal Edit -->
                        <div id="editModal{{ $trx->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 overflow-y-auto max-h-[90vh]">
                                <form action="{{ route('transaksi.update', $trx->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <h3 class="text-xl font-bold mb-4">Edit Transaksi</h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block mb-1">Tipe</label>
                                            <select name="tipe" class="w-full border rounded p-2" required>
                                                <option value="pemasukan" {{ $trx->tipe == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                                                <option value="pengeluaran" {{ $trx->tipe == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block mb-1">Jumlah</label>
                                            <input type="number" name="jumlah" class="w-full border rounded p-2" value="{{ $trx->jumlah }}" required>
                                        </div>
                                        <div>
                                            <label class="block mb-1">Tanggal Pengajuan</label>
                                            <input type="date" name="tanggal_pengajuan" class="w-full border rounded p-2" value="{{ $trx->tanggal_pengajuan }}" required>
                                        </div>
                                        <div>
                                            <label class="block mb-1">Tanggal Approval</label>
                                            <input type="date" name="tanggal_approval" class="w-full border rounded p-2" value="{{ $trx->tanggal_approval }}">
                                        </div>
                                    </div>

                                    <!-- Input Barang Dinamis -->
                                    <div class="mt-4">
                                        <label class="block mb-2">Barang</label>
                                        <div id="barang-wrapper-edit-{{ $trx->id }}">
                                            @foreach ($trx->details as $d)
                                            <div class="flex gap-2 mb-2 items-center">
                                                <select name="barang_id[]" class="border rounded p-2 flex-1" required>
                                                    <option value="">-- Pilih Barang --</option>
                                                    @foreach ($barang as $b)
                                                        <option value="{{ $b->id }}" {{ $d->barang_id == $b->id ? 'selected' : '' }}>{{ $b->nama_barang }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="number" name="barang_jumlah[]" class="border rounded p-2 w-24" value="{{ $d->jumlah }}" required>
                                                <button type="button" class="bg-green-600 text-white px-3 py-1 rounded add-barang">+</button>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="mt-6 flex justify-end gap-2">
                                        <button type="button" class="px-4 py-2 border rounded" onclick="closeModal('editModal{{ $trx->id }}')">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
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
                            @if(auth()->user()->role === 'A')
                                <option value="pemasukan">Pemasukan</option>
                            @endif
                            <option value="pengeluaran" selected>Pengeluaran</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1">Jumlah</label>
                        <input type="number" name="jumlah" class="w-full border rounded p-2" required>
                    </div>
                    <div>
                        <label class="block mb-1">Tanggal Pengajuan</label>
                        <input type="date" name="tanggal_pengajuan" class="w-full border rounded p-2" required>
                    </div>
                </div>

                <!-- Input Barang Dinamis -->
                <div class="mt-4">
                    <label class="block mb-2">Barang</label>
                    <div id="barang-wrapper">
                        <div class="flex gap-2 mb-2 items-center">
                            <select name="barang_id[]" class="border rounded p-2 flex-1" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($barang as $b)
                                    <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="barang_jumlah[]" class="border rounded p-2 w-24" placeholder="Jumlah" required>
                            <button type="button" class="bg-green-600 text-white px-3 py-1 rounded add-barang">+</button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 border rounded" onclick="closeModal('createModal')">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Modal & Barang Dinamis -->
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        document.addEventListener("DOMContentLoaded", () => {
            // Tambah Barang Dinamis di Tambah
            document.querySelector("#barang-wrapper").addEventListener("click", function(e) {
                if(e.target.classList.contains("add-barang")) {
                    let row = e.target.closest("div.flex");
                    let clone = row.cloneNode(true);
                    clone.querySelector("select").value = "";
                    clone.querySelector("input").value = "";
                    row.after(clone);
                }
            });

            // Tambah Barang Dinamis di Edit (untuk setiap modal edit)
            document.querySelectorAll("[id^=barang-wrapper-edit-]").forEach(wrapper => {
                wrapper.addEventListener("click", function(e) {
                    if(e.target.classList.contains("add-barang")) {
                        let row = e.target.closest("div.flex");
                        let clone = row.cloneNode(true);
                        clone.querySelector("select").value = "";
                        clone.querySelector("input").value = "";
                        row.after(clone);
                    }
                });
            });
        });
    </script>
</x-app-layout>
