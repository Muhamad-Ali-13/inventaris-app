<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Transaksi Inventaris') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('transaksi.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Departemen</label>
                        <select name="departemen_id" class="w-full border rounded p-2" required>
                            <option value="">Pilih Departemen</option>
                            @foreach($departemen as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Tipe Transaksi</label>
                        <select name="tipe" class="w-full border rounded p-2" required>
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700 mb-2">Barang</label>
                        <div id="barangContainer">
                            <div class="flex gap-2 mb-2">
                                <select name="barang_id[]" class="border rounded p-2 w-1/2">
                                    <option value="">Pilih Barang</option>
                                    @foreach($barang as $b)
                                        <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="jumlah[]" placeholder="Jumlah" class="border rounded p-2 w-1/2">
                            </div>
                        </div>
                        <button type="button" id="addRowBtn" class="bg-blue-500 text-white px-3 py-1 rounded">+ Tambah Barang</button>
                    </div>

                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('addRowBtn').addEventListener('click', function() {
            let container = document.getElementById('barangContainer');
            let newRow = document.createElement('div');
            newRow.classList.add('flex','gap-2','mb-2');
            newRow.innerHTML = `
                <select name="barang_id[]" class="border rounded p-2 w-1/2">
                    <option value="">Pilih Barang</option>
                    @foreach($barang as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                    @endforeach
                </select>
                <input type="number" name="jumlah[]" placeholder="Jumlah" class="border rounded p-2 w-1/2">
            `;
            container.appendChild(newRow);
        });
    </script>
</x-app-layout>
