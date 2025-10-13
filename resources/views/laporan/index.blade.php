<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            📊 Laporan Transaksi
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
                <div class="p-6 text-gray-800">

                    {{-- Filter Form --}}
                    <form method="GET" action="{{ route('laporan.index') }}"
                        class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Dari Tanggal --}}
                        <div>
                            <label for="tanggal_awal" class="block text-sm font-semibold text-gray-600 mb-1">
                                Dari Tanggal
                            </label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal"
                                value="{{ $tanggal_awal }}"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">
                        </div>

                        {{-- Sampai Tanggal --}}
                        <div>
                            <label for="tanggal_akhir" class="block text-sm font-semibold text-gray-600 mb-1">
                                Sampai Tanggal
                            </label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir"
                                value="{{ $tanggal_akhir }}"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">
                        </div>

                        {{-- Tombol --}}
                        <div class="flex flex-wrap gap-2 md:justify-end items-end mt-4 md:mt-0">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
                                Filter
                            </button>
                            <a href="{{ route('laporan.index') }}"
                                class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white font-semibold rounded-lg shadow transition">
                                Reset
                            </a>
                            <a href="{{ route('laporan.exportPdf', request()->all()) }}"
                                class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow transition">
                                Export PDF
                            </a>
                        </div>
                    </form>

                    {{-- 📱 Mobile View (Card Layout) --}}
                    <div class="block md:hidden space-y-5">
                        @forelse($transaksis as $trx)
                            <div
                                class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm transition hover:shadow-md">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-semibold text-lg text-gray-800">
                                        {{ $trx->departemen->nama_departemen ?? 'ADMIN' }}
                                    </h3>
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded 
                                        {{ $trx->status == 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($trx->status) }}
                                    </span>
                                </div>

                                <p class="text-sm text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($trx->tanggal_approval)->format('d M Y') }}
                                </p>

                                <div class="mt-3 space-y-1">
                                    @foreach ($trx->details as $detail)
                                        <p class="text-sm text-gray-700">
                                            {{ $detail->barang->nama_barang }}
                                            <span class="text-indigo-600 font-medium">({{ $detail->jumlah }})</span>
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

                    {{-- 💻 Desktop View (Table Layout) --}}
                    <div class="hidden md:block overflow-x-auto mt-8">
                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-indigo-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Departemen</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Barang</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Jumlah</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($transaksis as $trx)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            {{ \Carbon\Carbon::parse($trx->tanggal_approval)->format('d-m-Y') }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            {{ $trx->departemen->nama_departemen ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            @foreach ($trx->details as $detail)
                                                {{ $detail->barang->nama_barang }}<br>
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

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $transaksis->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
