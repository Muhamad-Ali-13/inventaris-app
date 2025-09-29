<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Transaksi') }}
        </h2>
    </x-slot>

    {{-- Content --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="GET" action="{{ route('laporan.index') }}"
                        class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">

                        {{-- Dari Tanggal --}}
                        <div>
                            <label for="tanggal_awal"
                                class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Dari
                                Tanggal</label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal" value="{{ $tanggal_awal }}"
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm">
                        </div>

                        {{-- Sampai Tanggal --}}
                        <div>
                            <label for="tanggal_akhir"
                                class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Sampai
                                Tanggal</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ $tanggal_akhir }}"
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm">
                        </div>

                        {{-- Tombol --}}
                        <div class="flex space-x-2">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Filter</button>
                            <a href="{{ route('laporan.index') }}"
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Reset</a>
                            <a href="{{ route('laporan.exportPdf', request()->all()) }}"
                                class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Export PDF</a>
                        </div>

                    </form>



                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        No</th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tanggal</th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Departemen</th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Barang</th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Jumlah</th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($transaksis as $trx)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($trx->tanggal_approval)->format('d-m-Y') }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $trx->departemen->nama_departemen ?? '-' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                            @foreach ($trx->details as $detail)
                                                {{ $detail->barang->nama_barang }} ({{ $detail->jumlah }})<br>
                                            @endforeach
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $trx->details->sum('jumlah') }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            <span
                                                class="px-2 py-1 text-xs rounded 
    {{ $trx->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($trx->status) }}
                                            </span>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-2 text-center text-sm text-gray-500">
                                            Tidak ada data transaksi
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $transaksis->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
