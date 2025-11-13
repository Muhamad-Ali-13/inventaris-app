<x-app-layout>
    <div class="py-10 bg-gradient-to-br from-blue-50 via-white to-blue-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Card Utama -->
            <div class="bg-white shadow-xl rounded-2xl border border-blue-100 p-6">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-blue-700">📊 Laporan Akuntansi Inventaris</h3>
                        <p class="text-gray-500 text-sm">Analisis nilai aset dan kesehatan inventaris.</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('laporan.index') }}"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm font-medium transition">
                            ← Kembali ke Laporan Transaksi
                        </a>
                    </div>
                </div>

                <!-- Form Filter -->
                <form method="GET" action="{{ route('laporan.akuntansi') }}" class="space-y-4 mb-8 w-full">
                    <!-- Grid Baris Pertama -->
                    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                        <!-- Dari Tanggal -->
                        <div>
                            <label for="tanggal_awal" class="block text-sm font-semibold text-gray-700 mb-1">
                                Dari Tanggal
                            </label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal" value="{{ $tanggal_awal }}"
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm">
                        </div>

                        <!-- Sampai Tanggal -->
                        <div>
                            <label for="tanggal_akhir" class="block text-sm font-semibold text-gray-700 mb-1">
                                Sampai Tanggal
                            </label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ $tanggal_akhir }}"
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm">
                        </div>
                    </div>

                    <!-- Tombol Filter & Reset -->
                    <div class="flex gap-2 justify-end">
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition">
                            <i class="fi fi-rr-filter text-sm"></i> Filter
                        </button>
                        <a href="{{ route('laporan.akuntansi') }}"
                            class="flex items-center gap-2 px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white font-semibold rounded-lg shadow transition">
                            <i class="fi fi-rr-rotate-left text-sm"></i> Reset
                        </a>
                        <!-- Tombol Export -->
                        <div class="flex justify-end">
                            <a href="{{ route('laporan.akuntansi.exportPdf', request()->all()) }}"
                                class="flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg shadow transition">
                                <i class="fi fi-rr-print text-sm"></i> Cetak PDF
                            </a>
                        </div>
                    </div>

                    <!-- Tabel Laporan Akuntansi -->
                    <div class="overflow-x-auto mt-8">
                        <table class="min-w-full border border-blue-100 rounded-lg overflow-hidden shadow-sm">
                            <thead class="bg-blue-100 text-blue-800 uppercase text-xs font-semibold">
                                <tr>
                                    <th class="px-4 py-3 text-left">No</th>
                                    <th class="px-4 py-3 text-left">Kode Barang</th>
                                    <th class="px-4 py-3 text-left">Nama Barang</th>
                                    <th class="px-4 py-3 text-left">Kategori</th>
                                    <th class="px-4 py-3 text-left">Qty</th>
                                    <th class="px-4 py-3 text-left">Harga Beli</th>
                                    <th class="px-4 py-3 text-left">Total Nilai</th>
                                    <th class="px-4 py-3 text-left">Tanggal Masuk</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($barangs as $barang)
                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $barang->kode_barang }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $barang->nama_barang }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            {{ $barang->kategori->nama_kategori ?? '-' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $barang->qty }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">Rp
                                            {{ number_format($barang->harga_beli) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700 font-semibold">Rp
                                            {{ number_format($barang->total_harga) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            {{ $barang->created_at->format('d-m-Y') }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded 
                                            {{ $barang->qty > 10 ? 'bg-green-100 text-green-700' : ($barang->qty > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $barang->qty > 10 ? 'Aman' : ($barang->qty > 0 ? 'Terbatas' : 'Habis') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-3 text-center text-sm text-gray-500">
                                            Tidak ada data inventaris
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-blue-50">
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-sm font-semibold text-blue-800">Total Nilai
                                        Inventaris:</td>
                                    <td colspan="3" class="px-4 py-3 text-sm font-bold text-blue-800">
                                        Rp {{ number_format($barangs->sum('total_harga')) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
            </div>
            </form>
        </div>
    </div>
    </div>
</x-app-layout>
