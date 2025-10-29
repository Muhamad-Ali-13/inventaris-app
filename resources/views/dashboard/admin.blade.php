<x-app-layout>

    <div class="py-8 bg-gradient-to-br from-gray-50 via-white to-blue-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow text-center hover:shadow-lg transition">
                    <h3 class="text-gray-600 font-medium">Jumlah Karyawan</h3>
                    <p class="text-4xl font-bold text-green-600 mt-2">{{ $jumlahKaryawan }}</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow text-center hover:shadow-lg transition">
                    <h3 class="text-gray-600 font-medium">Jumlah Barang</h3>
                    <p class="text-4xl font-bold text-blue-600 mt-2">{{ $jumlahBarang }}</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow text-center hover:shadow-lg transition">
                    <h3 class="text-gray-600 font-medium">Jumlah Transaksi</h3>
                    <p class="text-4xl font-bold text-yellow-600 mt-2">{{ $jumlahTransaksi }}</p>
                </div>
            </div>

            <!-- Grafik -->
            <div class="bg-white p-8 rounded-2xl shadow-xl">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Grafik Pemasukan & Pengeluaran</h3>
                <canvas id="chartTransaksi" height="100"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('chartTransaksi').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($bulanLabels) !!},
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: {!! json_encode($dataPemasukan) !!},
                            borderColor: 'rgba(34,197,94,1)',
                            backgroundColor: 'rgba(34,197,94,0.2)',
                            tension: 0.4,
                            fill: true,
                        },
                        {
                            label: 'Pengeluaran',
                            data: {!! json_encode($dataPengeluaran) !!},
                            borderColor: 'rgba(239,68,68,1)',
                            backgroundColor: 'rgba(239,68,68,0.2)',
                            tension: 0.4,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        </script>
    @endpush
</x-app-layout>
