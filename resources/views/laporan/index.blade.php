<x-app-layout>
    <div class="py-10 bg-gradient-to-br from-green-50 via-white to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Card Utama -->
            <div class="bg-white shadow-xl rounded-2xl border border-green-100 p-6">

                <!-- Header -->
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-green-700">📊 Laporan Transaksi</h3>
                    <p class="text-gray-500 text-sm">Kelola dan pantau laporan transaksi secara real-time.</p>
                </div>

                <!-- Form Filter -->
                <form method="GET" action="{{ route('laporan.index') }}" class="space-y-4 mb-8 w-full">

                    <!-- Grid Baris Pertama -->
                    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                        <!-- Dari Tanggal -->
                        <div>
                            <label for="tanggal_awal" class="block text-sm font-semibold text-gray-700 mb-1">
                                Dari Tanggal
                            </label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal" value="{{ $tanggal_awal }}"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm">
                        </div>

                        <!-- Sampai Tanggal -->
                        <div>
                            <label for="tanggal_akhir" class="block text-sm font-semibold text-gray-700 mb-1">
                                Sampai Tanggal
                            </label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ $tanggal_akhir }}"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm">
                        </div>

                    </div>

                    <!-- Grid Baris Kedua -->
                    <div class="w-full grid grid-cols-1 md:grid-cols-6 gap-4 items-end mt-4">

                        <!-- Jenis Transaksi -->
                        <div class="md:col-span-1">
                            <label for="jenis" class="block text-sm font-semibold text-gray-700 mb-1">
                                Jenis Transaksi
                            </label>
                            <select name="jenis" id="jenis"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm">
                                <option value="" {{ $jenis == null ? 'selected' : '' }}>-- Semua Jenis --</option>
                                <option value="pemasukan" {{ $jenis == 'pemasukan' ? 'selected' : '' }}>Pemasukan
                                </option>
                                <option value="pengeluaran" {{ $jenis == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran
                                </option>
                            </select>
                        </div>

                        <!-- Departemen -->
                        <div class="md:col-span-1">
                            <label for="departemen_id" class="block text-sm font-semibold text-gray-700 mb-1">
                                Departemen
                            </label>
                            <select name="departemen_id" id="departemen_id"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm">
                                <option value="" {{ $departemen_id == null ? 'selected' : '' }}>-- Semua
                                    Departemen --</option>
                                @foreach ($departemens as $departemen)
                                    <option value="{{ $departemen->id }}"
                                        {{ $departemen_id == $departemen->id ? 'selected' : '' }}>
                                        {{ $departemen->nama_departemen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tombol Filter & Reset -->
                        <div class="md:col-span-2 flex flex-wrap gap-2">
                            <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition w-full md:w-auto justify-center">
                                <i class="fi fi-rr-filter text-sm"></i> Filter
                            </button>
                            <a href="{{ route('laporan.index') }}"
                                class="flex items-center gap-2 px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white font-semibold rounded-lg shadow transition w-full md:w-auto justify-center">
                                <i class="fi fi-rr-rotate-left text-sm"></i> Reset
                            </a>
                        </div>

                        <!-- Tombol Export -->
                        <div class="md:col-span-2 flex flex-wrap gap-2 justify-end">
                            <a href="{{ route('laporan.exportPdf', request()->all()) }}"
                                class="flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg shadow transition w-full md:w-auto justify-center">
                                <i class="fi fi-rr-print text-sm"></i> Cetak PDF
                            </a>
                            <a href="{{ route('laporan.exportExcel', request()->all()) }}"
                                class="flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow transition w-full md:w-auto justify-center">
                                <i class="fi fi-rr-file-excel text-sm"></i> Export Excel
                            </a>
                        </div>

                    </div>

                </form>

                <!-- Tampilan Mobile -->
                <div class="block md:hidden space-y-5">
                    @forelse($transaksis as $trx)
                        <div
                            class="bg-white border border-green-100 rounded-xl p-4 shadow-sm hover:shadow-md transition">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-lg text-green-700">
                                    {{ $trx->departemen->nama_departemen ?? 'ADMIN' }}
                                </h3>
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded 
                                    {{ $trx->status == 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ $trx->tanggal_disetujui ? \Carbon\Carbon::parse($trx->tanggal_approval)->format('d M Y') : '-' }}
                            </p>

                            <div class="mt-3 space-y-1">
                                @foreach ($trx->details as $detail)
                                    <p class="text-sm text-gray-700">
                                        {{ $detail->barang->nama_barang }}
                                        <span class="text-green-600 font-medium">
                                            ({{ $detail->jumlah }})
                                        </span>
                                    </p>
                                @endforeach
                            </div>

                            <div class="mt-3 text-sm text-gray-600 border-t border-gray-100 pt-2">
                                Total Barang:
                                <span class="font-semibold text-gray-800">
                                    {{ $trx->details->sum('jumlah') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 text-sm">Tidak ada data transaksi</p>
                    @endforelse
                </div>

                <!-- Tampilan Desktop -->
                <div class="hidden md:block overflow-x-auto mt-8">
                    <table class="min-w-full border border-green-100 rounded-lg overflow-hidden shadow-sm">
                        <thead class="bg-green-100 text-green-800 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">Departemen</th>
                                <th class="px-4 py-3 text-left">Jenis Transaksi</th>
                                <th class="px-4 py-3 text-left">Barang</th>
                                <th class="px-4 py-3 text-left">Jumlah</th>
                                <th class="px-4 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($transaksis as $trx)
                                <tr class="hover:bg-green-50 transition">
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $trx->tanggal_disetujui ? \Carbon\Carbon::parse($trx->tanggal_approval)->format('d-m-Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $trx->user->name ?? 'ADMIN' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $trx->departemen->nama_departemen ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ ucfirst($trx->jenis ?? '-') }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        @foreach ($trx->details as $detail)
                                            {{ $detail->barang->nama_barang }}[{{ $detail->jumlah }}{{ $detail->barang->satuan }}]<br>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $trx->details->sum('jumlah') }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded 
                                            {{ $trx->status == 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ ucfirst($trx->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">
                                        Tidak ada data transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $transaksis->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
