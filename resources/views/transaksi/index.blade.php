<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Transaksi Inventaris') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg w-full p-4">

                <div class="p-4 bg-gray-100 mb-4 rounded-xl font-bold flex justify-between items-center">
                    <div>Transaksi Inventaris</div>
                    <div>
                        <a href="{{ route('transaksi.create') }}" class="bg-sky-400 p-2 text-white rounded-xl">Tambah</a>
                    </div>
                </div>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">NO</th>
                                <th class="px-6 py-3">TANGGAL</th>
                                <th class="px-6 py-3">DEPARTEMEN</th>
                                <th class="px-6 py-3">TIPE</th>
                                <th class="px-6 py-3">STATUS</th>
                                <th class="px-6 py-3">DETAIL</th>
                                @can('role-A')
                                <th class="px-6 py-3">ACTION</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($transaksi as $t)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4">{{ $no++ }}</td>
                                    <td>{{ \Carbon\Carbon::parse($t->tanggal_pengajuan)->format('d-m-Y') }}</td>
                                    <td class="px-6 py-4">{{ $t->departemen->nama_departemen }}</td>
                                    <td class="px-6 py-4 capitalize">{{ $t->tipe }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 rounded-xl text-white 
                                            @if ($t->status == 'pending') bg-yellow-500 
                                            @elseif($t->status == 'approved') bg-green-500 
                                            @else bg-red-500 @endif">
                                            {{ strtoupper($t->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <ul>
                                            @foreach ($t->details as $d)
                                                <li>{{ $d->barang->nama_barang }} ({{ $d->jumlah }})</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    @can('role-A')
                                    <td class="px-6 py-4 flex gap-2">
                                        <form action="{{ route('transaksi.approve', $t->id) }}" method="POST">
                                            @csrf
                                            <button class="bg-green-500 text-white px-2 py-1 rounded">Approve</button>
                                        </form>
                                        <form action="{{ route('transaksi.reject', $t->id) }}" method="POST">
                                            @csrf
                                            <button class="bg-red-500 text-white px-2 py-1 rounded">Reject</button>
                                        </form>
                                    </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
