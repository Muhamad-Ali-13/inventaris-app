{{-- resources/views/dashboard/direktur.blade.php --}}
<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <div class="min-h-screen bg-white py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-green-700 flex items-center gap-3">
                        <svg class="w-8 h-8 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            aria-hidden>
                            <path d="M3 3h18v4H3z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M3 13h18v8H3z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Dashboard Direktur — Ringkasan Inventaris
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Ringkasan inventaris & aktivitas transaksi
                        (pemasukan/pengeluaran).</p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-sm text-gray-500">Bulan:
                        <span class="font-semibold text-green-700">{{ now()->translatedFormat('F Y') }}</span>
                    </div>

                    <a href="{{ route('transaksi.index') }}"
                        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg shadow">
                        <i class="fi fi-rr-eye"></i> Lihat Transaksi
                    </a>
                </div>
            </div>

            <!-- Top summary (cards) -->
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

            <!-- Main Grid: left = chart + recent, right = optional accounting summary (keystats) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left (Chart + Recent) -->
                <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Grafik Pemasukan & Pengeluaran</h3>
                            <p class="text-sm text-gray-500">Per bulan (tahun berjalan)</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- small legend badges -->
                            <span class="flex items-center gap-2 text-sm text-gray-600">
                                <span class="w-2 h-2 rounded-full bg-green-600 inline-block"></span> Pemasukan
                            </span>
                            <span class="flex items-center gap-2 text-sm text-gray-600">
                                <span class="w-2 h-2 rounded-full bg-red-600 inline-block"></span> Pengeluaran
                            </span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <canvas id="chartDirektur" height="160"></canvas>
                    </div>

                    <div>
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Transaksi Terbaru</h4>
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
                                                @if ($t->status === 'pending')
                                                    {{ $t->tanggal_pengajuan instanceof \Carbon\Carbon ? $t->tanggal_pengajuan->format('d M Y') : $t->tanggal_pengajuan }}
                                                @else
                                                    {{ $t->tanggal_disetujui ? \Carbon\Carbon::parse($t->tanggal_disetujui)->format('d M Y') : '-' }}
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-gray-800">{{ $t->user->name ?? '-' }}</td>
                                            <td class="px-4 py-2 text-gray-700">
                                                {{ $t->departemen->nama_departemen ?? '-' }}</td>
                                            <td class="px-4 py-2 text-gray-700 capitalize">{{ $t->jenis }}</td>
                                            <td class="px-4 py-2">
                                                @if ($t->status === 'pending')
                                                    <span
                                                        class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                                                @elseif ($t->status === 'approved')
                                                    <span
                                                        class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Approved</span>
                                                @else
                                                    <span
                                                        class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Rejected</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada
                                                transaksi</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right (Quick Accounting Summary) -->
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Ringkasan Akuntansi</h3>
                    <p class="text-sm text-gray-500 mb-4">Total nilai pemasukan & pengeluaran (berdasarkan transaksi)
                    </p>

                    <div class="space-y-3">
                        <div
                            class="flex items-center justify-between bg-green-50 p-3 rounded-lg border border-green-100">
                            <div>
                                <p class="text-xs text-gray-500">Total Pemasukan (nilai)</p>
                                <p class="font-bold text-green-700 text-lg">Rp
                                    {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-green-600 text-2xl"><i class="fi fi-rr-arrow-up"></i></div>
                        </div>

                        <div class="flex items-center justify-between bg-red-50 p-3 rounded-lg border border-red-100">
                            <div>
                                <p class="text-xs text-gray-500">Total Pengeluaran (nilai)</p>
                                <p class="font-bold text-red-600 text-lg">Rp
                                    {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-red-600 text-2xl"><i class="fi fi-rr-arrow-down"></i></div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('laporan.index') }}"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow">
                                <i class="fi fi-rr-file"></i> Lihat Laporan Lengkap
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer / optional additional widgets -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="col-span-2 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold mb-3">Insight Singkat</h3>
                    <p class="text-sm text-gray-600">Gunakan grafik dan ringkasan ini untuk melihat tren bulanan
                        pemasukan dan pengeluaran. Untuk detail per barang / karyawan, buka halaman Laporan.</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold mb-3">Notifikasi</h3>
                    <p class="text-sm text-gray-600">{{ $pendingTransaksi }} transaksi menunggu persetujuan. Silakan
                        periksa dan setujui atau tolak sesuai kebutuhan.</p>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function() {
                const labels = {!! json_encode($bulanLabels) !!};
                const pemasukan = {!! json_encode($dataPemasukan) !!};
                const pengeluaran = {!! json_encode($dataPengeluaran) !!};

                const ctx = document.getElementById('chartDirektur').getContext('2d');

                const gradientIn = ctx.createLinearGradient(0, 0, 0, 300);
                gradientIn.addColorStop(0, 'rgba(16,185,129,0.35)');
                gradientIn.addColorStop(1, 'rgba(16,185,129,0)');

                const gradientOut = ctx.createLinearGradient(0, 0, 0, 300);
                gradientOut.addColorStop(0, 'rgba(239,68,68,0.35)');
                gradientOut.addColorStop(1, 'rgba(239,68,68,0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                                label: 'Pemasukan',
                                data: pemasukan,
                                borderColor: 'rgba(16,185,129,1)',
                                backgroundColor: gradientIn,
                                fill: true,
                                tension: 0.3,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            },
                            {
                                label: 'Pengeluaran',
                                data: pengeluaran,
                                borderColor: 'rgba(239,68,68,1)',
                                backgroundColor: gradientOut,
                                fill: true,
                                tension: 0.3,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const v = context.raw || 0;
                                        // If controller provided counts (not money), still format numerically
                                        if (typeof v === 'number') {
                                            return context.dataset.label + ': ' + v.toLocaleString('id-ID');
                                        }
                                        return context.dataset.label + ': ' + v;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#4B5563'
                                },
                                grid: {
                                    color: '#F3F4F6'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#6B7280'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            })();
        </script>
    @endpush
</x-app-layout>
