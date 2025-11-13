{{-- resources/views/dashboard/admin.blade.php --}}
<x-app-layout>
    <div class="min-h-screen bg-white py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-green-700 flex items-center gap-3">
                        <svg class="w-8 h-8 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 3h18v4H3z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 13h18v8H3z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Dashboard Admin — Inventaris & Akuntansi
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Ringkasan inventaris dan nilai persediaan perusahaan.</p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-sm text-gray-500">Bulan: <span class="font-semibold text-green-700">{{ now()->translatedFormat('F Y') }}</span></div>
                </div>
            </div>

            <!-- Top summary (Inventaris) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Jumlah Karyawan</p>
                            <p class="text-2xl font-bold text-green-700">{{ number_format($jumlahKaryawan) }}</p>
                        </div>
                        <div class="text-green-600 text-3xl"><i class="fi fi-rr-users-alt"></i></div>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Jumlah Barang</p>
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($jumlahBarang) }}</p>
                        </div>
                        <div class="text-blue-600 text-3xl"><i class="fi fi-rr-boxes"></i></div>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Transaksi (total)</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ number_format($jumlahTransaksi) }}</p>
                        </div>
                        <div class="text-yellow-600 text-3xl"><i class="fi fi-rr-exchange"></i></div>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Pending</p>
                            <p class="text-2xl font-bold text-red-600">{{ number_format($pendingTransaksi) }}</p>
                        </div>
                        <div class="text-red-600 text-3xl"><i class="fi fi-rr-clock"></i></div>
                    </div>
                </div>
            </div>

            <!-- Inventory & Accounting Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Inventory overview -->
                <div class="col-span-2 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Inventaris — Ringkasan</h3>
                            <p class="text-sm text-gray-500">Detail stok & nilai persediaan.</p>
                        </div>

                    </div>

                    <!-- inventory stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                            <p class="text-xs text-gray-500">Nilai Persediaan</p>
                            <p class="text-xl font-bold text-green-700">Rp {{ number_format($totalNilaiPersediaan, 0, ',', '.') }}</p>
                        </div>

                        <div class="p-4 bg-white rounded-lg border border-gray-100">
                            <p class="text-xs text-gray-500">Barang Stok Rendah</p>
                            <p class="text-xl font-bold text-red-600">{{ $stokRendah->count() }}</p>
                        </div>

                        <div class="p-4 bg-white rounded-lg border border-gray-100">
                            <p class="text-xs text-gray-500">Top Barang (nilai)</p>
                            <p class="text-xl font-bold text-gray-800">{{ $topBarangTerbesar->count() }}</p>
                        </div>
                    </div>

                    <!-- table top barang -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-gray-600">No</th>
                                    <th class="px-4 py-2 text-gray-600">Nama Barang</th>
                                    <th class="px-4 py-2 text-gray-600">Qty</th>
                                    <th class="px-4 py-2 text-gray-600">Harga Beli</th>
                                    <th class="px-4 py-2 text-gray-600">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topBarangTerbesar as $i => $b)
                                    <tr class="border-t">
                                        <td class="px-4 py-2 text-gray-700">{{ $i + 1 }}</td>
                                        <td class="px-4 py-2 text-gray-800 font-medium">{{ $b->nama_barang }}</td>
                                        <td class="px-4 py-2 text-gray-700">{{ number_format($b->qty) }}</td>
                                        <td class="px-4 py-2 text-gray-700">Rp {{ number_format($b->harga_beli ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-gray-900 font-semibold">Rp {{ number_format($b->total_harga ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                @if ($topBarangTerbesar->isEmpty())
                                    <tr><td colspan="5" class="px-4 py-4 text-center text-gray-400">Tidak ada data</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right: Accounting summary -->
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Akuntansi Barang</h3>
                    <p class="text-sm text-gray-500 mb-4">Ringkasan nilai pemasukan & pengeluaran (approved)</p>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between bg-green-50 p-3 rounded-lg border border-green-100">
                            <div>
                                <p class="text-xs text-gray-500">Total Pemasukan (nilai)</p>
                                <p class="font-bold text-green-700 text-lg">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-green-600 text-2xl"><i class="fi fi-rr-arrow-up"></i></div>
                        </div>

                        <div class="flex items-center justify-between bg-red-50 p-3 rounded-lg border border-red-100">
                            <div>
                                <p class="text-xs text-gray-500">Total Pengeluaran (nilai)</p>
                                <p class="font-bold text-red-600 text-lg">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-red-600 text-2xl"><i class="fi fi-rr-arrow-down"></i></div>
                        </div>

                        <div class="mt-4">
                            <canvas id="chartAkuntansi" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaksi Terbaru -->
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-4">Transaksi Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-600">Tanggal</th>
                                <th class="px-4 py-2 text-left text-gray-600">Nama</th>
                                <th class="px-4 py-2 text-left text-gray-600">Departemen</th>
                                <th class="px-4 py-2 text-left text-gray-600">Jenis</th>
                                <th class="px-4 py-2 text-left text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksiTerbaru as $t)
                                <tr class="border-t hover:bg-green-50">
                                    <td class="px-4 py-2 text-gray-700">
                                        @if($t->status === 'pending')
                                            {{ \Carbon\Carbon::parse($t->tanggal_pengajuan)->format('d M Y') }}
                                        @else
                                            {{ $t->tanggal_disetujui ? \Carbon\Carbon::parse($t->tanggal_disetujui)->format('d M Y') : '-' }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-gray-800">{{ $t->user->name ?? '-' }}</td>
                                    <td class="px-4 py-2 text-gray-700">{{ $t->departemen->nama_departemen ?? '-' }}</td>
                                    <td class="px-4 py-2 text-gray-700 capitalize">{{ $t->jenis }}</td>
                                    <td class="px-4 py-2">
                                        @if ($t->status === 'pending')
                                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                                        @elseif ($t->status === 'approved')
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Approved</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada transaksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Data from controller
            const labels = {!! json_encode($bulanLabels) !!};
            const pemasukan = {!! json_encode($dataPemasukan) !!};
            const pengeluaran = {!! json_encode($dataPengeluaran) !!};

            const ctx = document.getElementById('chartAkuntansi').getContext('2d');

            const gradIn = ctx.createLinearGradient(0, 0, 0, 300);
            gradIn.addColorStop(0, 'rgba(16,185,129,0.35)');
            gradIn.addColorStop(1, 'rgba(16,185,129,0)');

            const gradOut = ctx.createLinearGradient(0, 0, 0, 300);
            gradOut.addColorStop(0, 'rgba(239,68,68,0.35)');
            gradOut.addColorStop(1, 'rgba(239,68,68,0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Pemasukan (Rp)',
                            data: pemasukan,
                            borderColor: 'rgba(16,185,129,1)',
                            backgroundColor: gradIn,
                            fill: true,
                            tension: 0.3,
                        },
                        {
                            label: 'Pengeluaran (Rp)',
                            data: pengeluaran,
                            borderColor: 'rgba(239,68,68,1)',
                            backgroundColor: gradOut,
                            fill: true,
                            tension: 0.3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let v = context.raw || 0;
                                    return context.dataset.label + ': Rp ' + v.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
