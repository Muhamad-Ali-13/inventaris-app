<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-white via-blue-50 to-green-50 py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-10">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-extrabold text-blue-700 flex items-center gap-2">
                        <i class="fi fi-rr-briefcase text-blue-500"></i>
                        Dashboard Direktur
                    </h1>
                    <p class="text-gray-500 text-sm">Ringkasan analitis & performa sistem inventaris</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <span
                        class="px-4 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-400 text-white text-sm rounded-full font-semibold shadow-md">
                        {{ now()->format('F Y') }}
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div
                    class="group bg-white p-6 rounded-2xl shadow-lg border border-transparent hover:border-green-300 transition-all relative overflow-hidden hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-600 font-semibold">Jumlah Karyawan</h3>
                        <i class="fi fi-rr-users-alt text-green-500 text-3xl"></i>
                    </div>
                    <p class="text-5xl font-extrabold text-green-600">{{ $jumlahKaryawan }}</p>
                    <p class="text-sm text-gray-400 mt-2">Total staf terdaftar</p>
                </div>

                <div
                    class="group bg-white p-6 rounded-2xl shadow-lg border border-transparent hover:border-blue-300 transition-all relative overflow-hidden hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-600 font-semibold">Jumlah Barang</h3>
                        <i class="fi fi-rr-boxes text-blue-500 text-3xl"></i>
                    </div>
                    <p class="text-5xl font-extrabold text-blue-600">{{ $jumlahBarang }}</p>
                    <p class="text-sm text-gray-400 mt-2">Item dalam database</p>
                </div>

                <div
                    class="group bg-white p-6 rounded-2xl shadow-lg border border-transparent hover:border-yellow-300 transition-all relative overflow-hidden hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-600 font-semibold">Transaksi Bulan Ini</h3>
                        <i class="fi fi-rr-exchange text-yellow-500 text-3xl"></i>
                    </div>
                    <p class="text-5xl font-extrabold text-yellow-600">{{ $jumlahTransaksi }}</p>
                    <p class="text-sm text-gray-400 mt-2">Total seluruh transaksi</p>
                </div>

                <div
                    class="group bg-white p-6 rounded-2xl shadow-lg border border-transparent hover:border-red-300 transition-all relative overflow-hidden hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-600 font-semibold">Transaksi Pending</h3>
                        <i class="fi fi-rr-clock text-red-500 text-3xl"></i>
                    </div>
                    <p class="text-5xl font-extrabold text-red-600">{{ $pendingTransaksi }}</p>
                    <p class="text-sm text-gray-400 mt-2">Menunggu persetujuan</p>
                </div>
            </div>

            <!-- Grafik Tren -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Grafik Pemasukan & Pengeluaran</h3>
                <canvas id="chartDirektur" height="140"></canvas>
            </div>

            <!-- Transaksi Terbaru (Hanya Info) -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                <h3 class="text-lg font-semibold mb-4">Transaksi Terbaru</h3>
                <ul class="divide-y divide-gray-200">
                    @forelse($transaksiTerbaru as $transaksi)
                        <li class="py-2 flex justify-between">
                            <span>
                                @if ($transaksi->status === 'pending')
                                    {{ $transaksi->tanggal_pengajuan instanceof \Carbon\Carbon ? $transaksi->tanggal_pengajuan->format('d M Y') : $transaksi->tanggal_pengajuan }}
                                @else
                                    {{ $transaksi->tanggal_approval instanceof \Carbon\Carbon ? $transaksi->tanggal_approval->format('d M Y') : $transaksi->tanggal_approval }}
                                @endif
                                -
                                {{ $transaksi->user->name }}
                            </span>
                            <span class="capitalize font-medium">{{ $transaksi->status }}</span>
                        </li>
                    @empty
                        <li class="py-2 text-center text-gray-400">Tidak ada transaksi terbaru.</li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('chartDirektur').getContext('2d');
            const gradientIn = ctx.createLinearGradient(0, 0, 0, 400);
            gradientIn.addColorStop(0, 'rgba(16,185,129,0.4)');
            gradientIn.addColorStop(1, 'rgba(16,185,129,0)');

            const gradientOut = ctx.createLinearGradient(0, 0, 0, 400);
            gradientOut.addColorStop(0, 'rgba(239,68,68,0.4)');
            gradientOut.addColorStop(1, 'rgba(239,68,68,0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($bulanLabels) !!},
                    datasets: [{
                            label: 'Pemasukan',
                            data: {!! json_encode($dataPemasukan) !!},
                            borderColor: 'rgba(16,185,129,1)',
                            backgroundColor: gradientIn,
                            fill: true,
                            tension: 0.3,
                        },
                        {
                            label: 'Pengeluaran',
                            data: {!! json_encode($dataPengeluaran) !!},
                            borderColor: 'rgba(239,68,68,1)',
                            backgroundColor: gradientOut,
                            fill: true,
                            tension: 0.3,
                        }
                    ]
                },
                options: {
                    responsive: true
                }
            });
        </script>
    @endpush
</x-app-layout>
