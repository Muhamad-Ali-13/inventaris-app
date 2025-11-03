<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-white via-green-50 to-blue-50 py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-10">

            <!-- HEADER -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-extrabold text-green-700 flex items-center gap-2">
                        <i class="fi fi-rr-dashboard text-green-500"></i>
                        Dashboard Admin Inventaris
                    </h1>
                    <p class="text-gray-500 text-sm">Ringkasan aktivitas & performa sistem inventaris</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center gap-2">
                    <span class="px-4 py-1.5 bg-gradient-to-r from-green-500 to-emerald-400 text-white text-sm rounded-full font-semibold shadow-md">
                        {{ now()->format('F Y') }}
                    </span>
                </div>
            </div>

            <!-- STATISTIC CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="group bg-white p-6 rounded-2xl shadow-lg border border-transparent hover:border-green-300 transition-all relative overflow-hidden hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-600 font-semibold">Jumlah Karyawan</h3>
                        <i class="fi fi-rr-users-alt text-green-500 text-3xl"></i>
                    </div>
                    <p class="text-5xl font-extrabold text-green-600">{{ $jumlahKaryawan }}</p>
                    <p class="text-sm text-gray-400 mt-2">Total staf terdaftar</p>
                </div>

                <div class="group bg-white p-6 rounded-2xl shadow-lg border border-transparent hover:border-blue-300 transition-all relative overflow-hidden hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-600 font-semibold">Jumlah Barang</h3>
                        <i class="fi fi-rr-boxes text-blue-500 text-3xl"></i>
                    </div>
                    <p class="text-5xl font-extrabold text-blue-600">{{ $jumlahBarang }}</p>
                    <p class="text-sm text-gray-400 mt-2">Item dalam database</p>
                </div>

                <div class="group bg-white p-6 rounded-2xl shadow-lg border border-transparent hover:border-yellow-300 transition-all relative overflow-hidden hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-600 font-semibold">Transaksi Bulan Ini</h3>
                        <i class="fi fi-rr-exchange text-yellow-500 text-3xl"></i>
                    </div>
                    <p class="text-5xl font-extrabold text-yellow-600">{{ $jumlahTransaksi }}</p>
                    <p class="text-sm text-gray-400 mt-2">Total seluruh transaksi</p>
                </div>

                <div class="group bg-white p-6 rounded-2xl shadow-lg border border-transparent hover:border-red-300 transition-all relative overflow-hidden hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-600 font-semibold">Transaksi Pending</h3>
                        <i class="fi fi-rr-clock text-red-500 text-3xl"></i>
                    </div>
                    <p class="text-5xl font-extrabold text-red-600">{{ $pendingTransaksi }}</p>
                    <p class="text-sm text-gray-400 mt-2">Menunggu persetujuan</p>
                </div>
            </div>

            <!-- Stok Rendah Alert -->
            @if($stokRendah > 0)
            <div class="bg-red-100 border border-red-300 rounded-xl p-4 text-red-700 font-semibold">
                <i class="fi fi-rr-alert-triangle mr-2"></i>
                Ada {{ $stokRendah }} barang dengan stok rendah! Segera lakukan pengecekan.
            </div>
            @endif

            <!-- Tabel Transaksi Terbaru -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-lg font-semibold mb-4">Transaksi Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-green-700">Tanggal</th>
                                <th class="px-4 py-2 text-left font-semibold text-green-700">User</th>
                                <th class="px-4 py-2 text-left font-semibold text-green-700">Departemen</th>
                                <th class="px-4 py-2 text-left font-semibold text-green-700">Jenis</th>
                                <th class="px-4 py-2 text-left font-semibold text-green-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($transaksiTerbaru as $transaksi)
                            <tr>
                                <td class="px-4 py-2">{{ $transaksi->tanggal_pengajuan instanceof \Carbon\Carbon ? $transaksi->tanggal_pengajuan->format('d M Y') : $transaksi->tanggal_pengajuan }}</td>
                                <td class="px-4 py-2">{{ $transaksi->user->name ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $transaksi->departemen->nama_departemen ?? '-' }}</td>
                                <td class="px-4 py-2 capitalize">{{ $transaksi->jenis }}</td>
                                <td class="px-4 py-2">
                                    @if($transaksi->status === 'pending')
                                    <span class="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">Pending</span>
                                    @elseif($transaksi->status === 'approved')
                                    <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Approved</span>
                                    @else
                                    <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">Tidak ada transaksi terbaru.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CHART: Pemasukan & Pengeluaran -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-green-100">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-green-700 flex items-center gap-2">
                        <i class="fi fi-rr-chart-line text-green-600 text-xl"></i>
                        Grafik Pemasukan & Pengeluaran
                    </h3>
                    <span class="text-sm text-gray-500">12 Bulan Terakhir</span>
                </div>
                <canvas id="chartTransaksi" height="140"></canvas>
            </div>

            <!-- Footer -->
            <div class="text-center text-sm text-gray-400 pt-6">
                © {{ date('Y') }} <span class="text-green-600 font-semibold">Sistem Inventaris</span>. All rights reserved.
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- AOS Animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
        });
    </script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chartTransaksi').getContext('2d');
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
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: {!! json_encode($dataPemasukan) !!},
                        borderColor: 'rgba(16,185,129,1)',
                        backgroundColor: gradientIn,
                        pointBackgroundColor: 'rgba(16,185,129,1)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    },
                    {
                        label: 'Pengeluaran',
                        data: {!! json_encode($dataPengeluaran) !!},
                        borderColor: 'rgba(239,68,68,1)',
                        backgroundColor: gradientOut,
                        pointBackgroundColor: 'rgba(239,68,68,1)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    }
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#374151', boxWidth: 12 }
                    },
                    tooltip: {
                        backgroundColor: '#fff',
                        titleColor: '#111',
                        bodyColor: '#111',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        cornerRadius: 8,
                        titleFont: { weight: 'bold' },
                        bodyFont: { size: 12 }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#6b7280' },
                        grid: { color: '#f3f4f6' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#6b7280' },
                        grid: { color: '#f3f4f6' }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>