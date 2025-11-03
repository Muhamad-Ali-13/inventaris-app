<x-app-layout>
    @php
        $hour = now()->format('H');
        $greeting = match (true) {
            $hour < 12 => 'Selamat Pagi',
            $hour < 15 => 'Selamat Siang',
            $hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };
    @endphp

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 shadow-sm px-4 py-3 rounded-xl mx-4 mt-5 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="text-center md:text-left">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">
                {{ $greeting }}, <span class="text-green-600">{{ Auth::user()->name }}</span> 👋
            </h1>
            <p class="text-gray-600 mt-1 text-sm md:text-base">
                Departemen:
                <span class="font-semibold text-green-700">{{ $karyawan->departemen->nama_departemen ?? '-' }}</span>
            </p>
        </div>

        <div class="flex items-center space-x-3 text-center md:text-right">
            <img src="{{ asset('image/logo.png') }}" alt="Logo" class="w-10 h-10 md:w-12 md:h-12 rounded-md shadow-sm">
            <span class="text-gray-800 font-extrabold text-xs md:text-sm leading-tight tracking-wide">PT. Bank Perekonomian Rakyat<br>Artha Jaya Mandiri</span>
        </div>
    </header>

    <!-- Body -->
    <main class="py-10 bg-gradient-to-br from-white via-white to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            <!-- Statistik Transaksi Pribadi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-gradient-to-r from-green-600 to-emerald-500 text-white p-6 rounded-2xl shadow-lg transform hover:scale-[1.02] transition duration-300 ease-out">
                    <h3 class="text-base md:text-lg font-medium opacity-90">Total Transaksi Saya</h3>
                    <p class="text-4xl md:text-5xl font-extrabold mt-3 leading-tight">{{ $jumlahTransaksi }}</p>
                    <p class="mt-2 text-xs md:text-sm opacity-80">Sepanjang tahun ini</p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base md:text-lg font-semibold text-gray-700">Status Transaksi Terbaru</h3>
                        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700 font-medium whitespace-nowrap">{{ now()->translatedFormat('F Y') }}</span>
                    </div>

                    @if($transaksiPribadiTerbaru->isEmpty())
                        <p class="text-gray-500 mt-3 text-sm">Belum ada transaksi yang dibuat.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach($transaksiPribadiTerbaru as $transaksi)
                            <li class="py-2 flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ ucfirst($transaksi->jenis) }}</p>
                                    <td class="px-4 py-2">{{ $transaksi->tanggal_pengajuan instanceof \Carbon\Carbon ? $transaksi->tanggal_pengajuan->format('d M Y') : $transaksi->tanggal_pengajuan }}</td>
                                </div>
                                <span class="px-3 py-1 text-xs rounded-full 
                                    @if($transaksi->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($transaksi->status === 'approved') bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700
                                    @endif
                                    font-semibold capitalize whitespace-nowrap">
                                    {{ $transaksi->status }}
                                </span>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Grafik Transaksi Bulanan -->
            <section class="bg-white p-6 md:p-8 rounded-2xl shadow-xl border border-gray-100 overflow-hidden relative">
                <div class="flex items-center justify-between mb-6 gap-2">
                    <h3 class="text-lg md:text-xl font-semibold text-gray-800">Grafik Transaksi Bulanan</h3>
                    <p class="text-xs md:text-sm text-gray-500">Data real berdasarkan aktivitas transaksi</p>
                </div>
                <div class="relative w-full h-[250px] sm:h-[350px] md:h-[400px]">
                    <canvas id="chartTransaksi"></canvas>
                </div>
            </section>
        </div>
    </main>

    @push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chartTransaksi').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($bulanLabels) !!},
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: {!! json_encode($dataTransaksi) !!},
                    backgroundColor: 'rgba(34,197,94,0.7)',
                    borderColor: 'rgba(22,163,74,1)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#14532D',
                        titleColor: '#fff',
                        bodyColor: '#E5E7EB',
                        cornerRadius: 8,
                        borderColor: '#16A34A',
                        borderWidth: 1,
                        padding: 10,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#4B5563',
                            font: { size: 12, weight: '500' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#6B7280',
                            font: { size: 11 }
                        },
                        grid: { color: '#E5E7EB' }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>