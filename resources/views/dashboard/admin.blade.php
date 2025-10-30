<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-white via-green-50 to-blue-50 py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-10">

            <!-- HEADER -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6" data-aos="fade-down">
                <div>
                    <h1 class="text-3xl font-extrabold text-green-700 flex items-center gap-2">
                        <i class="fi fi-rr-dashboard text-green-500"></i>
                        Dashboard Inventaris
                    </h1>
                    <p class="text-gray-500 text-sm">Laporan ringkas aktivitas dan performa inventaris</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center gap-2">
                    <span
                        class="px-4 py-1.5 bg-gradient-to-r from-green-500 to-emerald-400 text-white text-sm rounded-full font-semibold shadow-md">
                        {{ now()->format('F Y') }}
                    </span>
                </div>
            </div>

            <!-- STATISTIC CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div data-aos="fade-up" data-aos-delay="100"
                    class="group bg-white p-6 rounded-2xl shadow-lg border border-transparent hover:border-green-300 hover:shadow-green-100 transition-all relative overflow-hidden hover:-translate-y-1">
                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-400 to-emerald-500 rounded-t-2xl animate-pulse">
                    </div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-600 font-semibold">Jumlah Karyawan</h3>
                        <i class="fi fi-rr-users-alt text-green-500 text-3xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <p class="text-5xl font-extrabold text-green-600">{{ $jumlahKaryawan }}</p>
                    <p class="text-sm text-gray-400 mt-2">Total staf terdaftar</p>
                </div>

                <!-- Card 2 -->
                <div data-aos="fade-up" data-aos-delay="200"
                    class="group bg-white p-6 rounded-2xl shadow-lg border border-transparent hover:border-blue-300 hover:shadow-blue-100 transition-all relative overflow-hidden hover:-translate-y-1">
                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-cyan-500 rounded-t-2xl animate-pulse">
                    </div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-600 font-semibold">Jumlah Barang</h3>
                        <i class="fi fi-rr-boxes text-blue-500 text-3xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <p class="text-5xl font-extrabold text-blue-600">{{ $jumlahBarang }}</p>
                    <p class="text-sm text-gray-400 mt-2">Item dalam database</p>
                </div>

                <!-- Card 3 -->
                <div data-aos="fade-up" data-aos-delay="300"
                    class="group bg-white p-6 rounded-2xl shadow-lg border border-transparent hover:border-yellow-300 hover:shadow-yellow-100 transition-all relative overflow-hidden hover:-translate-y-1">
                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-yellow-400 to-amber-500 rounded-t-2xl animate-pulse">
                    </div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-600 font-semibold">Jumlah Transaksi</h3>
                        <i class="fi fi-rr-exchange text-yellow-500 text-3xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <p class="text-5xl font-extrabold text-yellow-600">{{ $jumlahTransaksi }}</p>
                    <p class="text-sm text-gray-400 mt-2">Transaksi bulan ini</p>
                </div>
            </div>

            <!-- CHART SECTION -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-green-100" data-aos="fade-up" data-aos-delay="400">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-green-700 flex items-center gap-2">
                        <i class="fi fi-rr-chart-line text-green-600 text-xl"></i>
                        Grafik Pemasukan & Pengeluaran
                    </h3>
                    <span class="text-sm text-gray-500">6 Bulan Terakhir</span>
                </div>

                <div class="relative">
                    <canvas id="chartTransaksi" height="140"></canvas>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent pointer-events-none rounded-xl">
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="text-center text-sm text-gray-400 pt-6" data-aos="fade-up" data-aos-delay="500">
                © {{ date('Y') }} <span class="text-green-600 font-semibold">Sistem Inventaris</span>. All rights reserved.
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- AOS Animation Library -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({
                duration: 900,
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
                            tension: 0.4,
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
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: '#374151', boxWidth: 12 } },
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
