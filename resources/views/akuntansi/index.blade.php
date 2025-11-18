<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <div class="py-10 bg-gradient-to-br from-blue-50 via-white to-blue-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl border border-blue-100 p-6">

                <!-- HEADER -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-blue-700">📊 Laporan Akuntansi Inventaris</h3>
                        <p class="text-gray-500 text-sm">Analisis nilai aset dan kesehatan inventaris berdasarkan data
                            transaksi yang sudah disetujui.</p>
                    </div>
                    <a href="{{ route('laporan.index') }}"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm font-medium">
                        ← Kembali
                    </a>
                </div>

                <!-- FORM FILTER -->
                <form method="GET" action="{{ route('akuntansi.index') }}" class="space-y-4 mb-8 w-full">

                    <!-- Baris 1 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-700 font-semibold">Dari Tanggal</label>
                            <input type="date" name="tanggal_awal" value="{{ $tanggal_awal }}"
                                class="w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="text-sm text-gray-700 font-semibold">Sampai Tanggal</label>
                            <input type="date" name="tanggal_akhir" value="{{ $tanggal_akhir }}"
                                class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>

                    <!-- Baris 2 -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Kategori -->
                        <div>
                            <label class="text-sm font-semibold text-gray-700">Kategori</label>
                            <select name="kategori_id" class="w-full rounded-lg border-gray-300">
                                <option value="">-- Semua Kategori --</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}"
                                        {{ $kategori_id == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Departemen -->
                        <div>
                            <label class="text-sm font-semibold text-gray-700">Departemen</label>
                            <select name="departemen_id" class="w-full rounded-lg border-gray-300">
                                <option value="">-- Semua Departemen --</option>
                                @foreach ($departemens as $departemen)
                                    <option value="{{ $departemen->id }}"
                                        {{ $departemen_id == $departemen->id ? 'selected' : '' }}>
                                        {{ $departemen->nama_departemen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Aksi -->
                        <div class="flex gap-2 items-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow">
                                <i class="fi fi-rr-filter"></i> Filter
                            </button>
                            <a href="{{ route('akuntansi.index') }}"
                                class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow">
                                <i class="fi fi-rr-rotate-left"></i>
                            </a>
                            <a href="{{ route('akuntansi.exportPdf', request()->all()) }}"
                                class="px-4 py-2 bg-emerald-500 text-white rounded-lg shadow">
                                <i class="fi fi-rr-print"></i> PDF
                            </a>
                            <a href="{{ route('akuntansi.exportExcel', request()->all()) }}"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow">
                                <i class="fi fi-rr-file-excel"></i> Excel
                            </a>
                        </div>
                    </div>
                </form>

                <!-- TABEL AKUNTANSI -->
                <div class="overflow-x-auto mt-8">
                    <table class="min-w-full border border-blue-100 rounded-lg shadow-sm">
                        <thead class="bg-blue-100 text-blue-800 text-xs font-semibold uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-left">Kode Barang</th>
                                <th class="px-4 py-3 text-left">Nama Barang</th>
                                <th class="px-4 py-3 text-left">Departemen</th>
                                <th class="px-4 py-3 text-left">Jenis</th>
                                <th class="px-4 py-3 text-left">Kategori</th>
                                <th class="px-4 py-3 text-left">Qty</th>
                                <th class="px-4 py-3 text-left">Harga Beli</th>
                                <th class="px-4 py-3 text-left">Total Nilai</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($barangs as $barang)
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2">{{ $barang->kode_barang }}</td>
                                    <td class="px-4 py-2">{{ $barang->nama_barang }}</td>
                                    <td class="px-4 py-2">
                                        {{ $departemen->nama_departemen ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $transaksi->jenis ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $kategori->nama_kategori ?? 'Tidak Berkategori' }}
                                    </td>

                                    <td class="px-4 py-2">{{ $barang->total_qty }} {{ $barang->satuan }}</td>

                                    <td class="px-4 py-2">
                                        Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-2 font-semibold">
                                        Rp {{ number_format($barang->total_nilai, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-3 text-center text-gray-500">
                                        Tidak ada data inventaris.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        <tfoot class="bg-blue-50">
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-blue-800 font-semibold">
                                    Total Nilai Inventaris:
                                </td>
                                <td colspan="2" class="px-4 py-3 font-bold text-blue-800">
                                    Rp {{ number_format($barangs->sum('total_nilai'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
