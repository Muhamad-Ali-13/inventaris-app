<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <div class="py-10 bg-gradient-to-br from-gray-50 via-white to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">
                <!-- HEADER UTAMA: Judul dan Tombol Aksi Primer -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">📦 Data Barang</h3>
                        <p class="text-gray-500 text-sm">Kelola dan pantau stok barang secara real-time.</p>
                    </div>

                    @can('role-A')
                        <button onclick="tambahBarangModal()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg shadow transition text-sm font-semibold inline-flex items-center self-start md:self-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Barang
                        </button>
                    @endcan
                </div>
                <!-- PANEL KONTROL: Filter, Entries, dan Search -->
                {{-- PERUBAHAN: Membungkus filter dalam form GET dengan desain panel --}}
                <form action="{{ route('barang.index') }}" method="GET">
                    {{-- Input tersembunyi untuk mempertahankan sorting --}}
                    @if (request('sort_by'))
                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    @endif
                    @if (request('order'))
                        <input type="hidden" name="order" value="{{ request('order') }}">
                    @endif

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Kiri: Filter Entries dan Kategori -->
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Entries -->
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <label for="entries">Tampilkan</label>
                                <select name="entries" id="entries"
                                    class="border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500 bg-white"
                                    onchange="this.form.submit()">
                                    <option value="5" {{ request('entries') == 5 ? 'selected' : '' }}>5
                                    </option>
                                    <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10
                                    </option>
                                    <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25
                                    </option>
                                    <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50
                                    </option>
                                    <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100
                                    </option>
                                </select>
                                <span>entri</span>
                            </div>

                            <!-- Filter kategori -->
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <label for="kategoriFilter">Filter</label>
                                <select name="kategori_id" id="kategoriFilter"
                                    class="border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500 bg-white"
                                    onchange="this.form.submit()">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($kategori as $k)
                                        <option value="{{ $k->id }}"
                                            {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Kanan: Search Bar -->
                        <div class="relative w-full lg:w-96">
                            <input type="text" name="search" placeholder="Cari nama atau kode barang..."
                                value="{{ request('search') }}"
                                class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-green-500 focus:border-green-500 bg-white">
                            <i class="fi fi-rr-search absolute left-3 top-2.5 text-gray-400"></i>
                        </div>
                    </div>
                </form>

                <!-- DESKTOP TABLE -->
                <div class="overflow-x-auto mt-5 rounded-lg border border-gray-100 hidden md:block">
                    <table class="w-full text-sm text-left text-gray-700" id="barangTable">
                        <thead class="bg-green-100 text-green-800 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">No</th>

                                {{-- Header Sortable --}}
                                @php
                                    $column = 'kode_barang';
                                    $title = 'Kode Barang';
                                    $currentSort = request('sort_by', 'nama_barang');
                                    $currentOrder = request('order', 'asc');
                                    $newOrder = $column == $currentSort && $currentOrder == 'asc' ? 'desc' : 'asc';
                                    $queryParams = request()->query();
                                    $queryParams['sort_by'] = $column;
                                    $queryParams['order'] = $newOrder;
                                @endphp
                                <th class="px-6 py-3"><a href="{{ route('barang.index', $queryParams) }}"
                                        class="flex items-center font-semibold text-green-800 hover:underline">{{ $title }}
                                        @include('partials.sort-icon', ['column' => $column])</a></th>

                                <th class="px-6 py-3">Tanggal Masuk</th>

                                @php
                                    $column = 'nama_barang';
                                    $title = 'Nama Barang';
                                    $newOrder = $column == $currentSort && $currentOrder == 'asc' ? 'desc' : 'asc';
                                    $queryParams['sort_by'] = $column;
                                    $queryParams['order'] = $newOrder;
                                @endphp
                                <th class="px-6 py-3"><a href="{{ route('barang.index', $queryParams) }}"
                                        class="flex items-center font-semibold text-green-800 hover:underline">{{ $title }}
                                        @include('partials.sort-icon', ['column' => $column])</a></th>

                                @php
                                    $column = 'kategori_id';
                                    $title = 'Kategori';
                                    $newOrder = $column == $currentSort && $currentOrder == 'asc' ? 'desc' : 'asc';
                                    $queryParams['sort_by'] = $column;
                                    $queryParams['order'] = $newOrder;
                                @endphp
                                <th class="px-6 py-3"><a href="{{ route('barang.index', $queryParams) }}"
                                        class="flex items-center font-semibold text-green-800 hover:underline">{{ $title }}
                                        @include('partials.sort-icon', ['column' => $column])</a></th>

                                @php
                                    $column = 'harga_beli';
                                    $title = 'Harga Beli';
                                    $newOrder = $column == $currentSort && $currentOrder == 'asc' ? 'desc' : 'asc';
                                    $queryParams['sort_by'] = $column;
                                    $queryParams['order'] = $newOrder;
                                @endphp
                                <th class="px-6 py-3"><a href="{{ route('barang.index', $queryParams) }}"
                                        class="flex items-center font-semibold text-green-800 hover:underline">{{ $title }}
                                        @include('partials.sort-icon', ['column' => $column])</a></th>

                                @php
                                    $column = 'qty';
                                    $title = 'Qty';
                                    $newOrder = $column == $currentSort && $currentOrder == 'asc' ? 'desc' : 'asc';
                                    $queryParams['sort_by'] = $column;
                                    $queryParams['order'] = $newOrder;
                                @endphp
                                <th class="px-6 py-3"><a href="{{ route('barang.index', $queryParams) }}"
                                        class="flex items-center font-semibold text-green-800 hover:underline">{{ $title }}
                                        @include('partials.sort-icon', ['column' => $column])</a></th>

                                <th class="px-6 py-3">Satuan</th>
                                <th class="px-6 py-3">Total</th>
                                <th class="px-6 py-3">Keterangan</th>
                                @can('role-A')
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($barang as $b)
                                <tr class="border-b hover:bg-green-50 transition">
                                    <td class="px-4 py-2">{{ $barang->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-3 font-mono">{{ $b->kode_barang }}</td>
                                    <td class="px-6 py-3">{{ $b->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $b->nama_barang }}</td>
                                    <td class="px-6 py-3">{{ $b->kategori->nama_kategori }}</td>
                                    <td class="px-6 py-3">Rp {{ number_format($b->harga_beli, 0, ',', '.') }}</td>
                                    <td
                                        class="px-6 py-3 {{ $b->qty == 0 ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                                        {{ $b->qty }}</td>
                                    <td class="px-6 py-3">{{ $b->satuan }}</td>
                                    <td class="px-6 py-3">Rp {{ number_format($b->total_harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3">{{ $b->keterangan ?? '-' }}</td>
                                    @can('role-A')
                                        <td class="px-6 py-3 text-center flex justify-center gap-2">
                                            <button onclick="editBarangModal(this)" data-id="{{ $b->id }}"
                                                data-kode="{{ $b->kode_barang }}" data-nama="{{ $b->nama_barang }}"
                                                data-tanggal_masuk="{{ $b->created_at->format('Y-m-d') }}"
                                                data-kategori="{{ $b->kategori_id }}" data-harga="{{ $b->harga_beli }}"
                                                data-qty="{{ $b->qty }}" data-satuan="{{ $b->satuan }}"
                                                data-total="{{ $b->total_harga }}" data-keterangan="{{ $b->keterangan }}"
                                                title="Edit" class="text-blue-600 hover:text-blue-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    class="w-5 h-5 inline">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('barang.destroy', $b->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                                                class="flex-1">
                                                @csrf @method('DELETE')
                                                <button title="Hapus" class="text-red-600 hover:text-red-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3m-7 0h8" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="@can('role-A') 11 @else 10 @endcan"
                                        class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada data barang
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- MOBILE CARD VIEW -->
                <div class="md:hidden space-y-4" id="barangCards">
                    @forelse ($barang as $b)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-lg text-gray-800">{{ $b->kode_barang }}</h4>
                                <span
                                    class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">{{ $b->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-lg text-gray-800">{{ $b->nama_barang }}</h4>
                                <span
                                    class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">{{ $b->kategori->nama_kategori }}</span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>Harga:</strong> Rp {{ number_format($b->harga_beli, 0, ',', '.') }}</p>
                                <p><strong>Stok:</strong> <span
                                        class="{{ $b->qty == 0 ? 'text-red-500 font-semibold' : 'text-gray-800' }}">{{ $b->qty }}
                                        {{ $b->satuan }}</span></p>
                                <p><strong>Total Harga:</strong> Rp {{ number_format($b->total_harga, 0, ',', '.') }}
                                </p>
                                <p><strong>Keterangan:</strong> {{ $b->keterangan ?? '-' }}</p>
                            </div>
                            @can('role-A')
                                <div class="flex gap-2 mt-4">
                                    <button onclick="editBarangModal(this)" data-id="{{ $b->id }}"
                                        data-kode="{{ $b->kode_barang }}" data-nama="{{ $b->nama_barang }}"
                                        data-tanggal_masuk="{{ $b->created_at->format('Y-m-d') }}"
                                        data-kategori="{{ $b->kategori_id }}" data-harga="{{ $b->harga_beli }}"
                                        data-qty="{{ $b->qty }}" data-satuan="{{ $b->satuan }}"
                                        data-total="{{ $b->total_harga }}" data-keterangan="{{ $b->keterangan }}"
                                        title="Edit" class="text-blue-600 hover:text-blue-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-5 h-5 inline">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('barang.destroy', $b->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="flex-1">
                                        @csrf @method('DELETE')
                                        <button title="Hapus" class="text-red-600 hover:text-red-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3m-7 0h8" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>
                    @empty
                        <div
                            class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 text-center text-gray-500">
                            Tidak ada data barang
                        </div>
                    @endforelse
                </div>
                {{-- PERUBAHAN: Tambahkan Pagination --}}
                <div class="mt-6 flex justify-center">
                    {{ $barang->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH & EDIT (tidak berubah) -->
    {{-- ... Sisipkan kembali modal tambah dan edit Anda di sini ... --}}
    <!-- MODAL TAMBAH -->
    <div class="fixed inset-0 flex items-center justify-center z-50 hidden" id="tambahBarangModal">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl p-6 relative z-10">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-xl font-semibold text-green-700">Tambah Barang</h3>
            </div>
            <form method="POST" action="{{ route('barang.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                <div class="space-y-4">
                    <div class="mb-3"><label class="block text-sm font-medium">Kode Barang</label><input
                            type="text" name="kode_barang" value="{{ $kodeBaru }}" readonly
                            class="w-full border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"></div>
                    <div><label class="text-sm text-gray-600 font-medium">Tanggal Masuk</label><input type="date"
                            name="tanggal_masuk" required
                            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div><label class="text-sm text-gray-600 font-medium">Nama Barang</label><input type="text"
                            name="nama_barang" required placeholder="Contoh: Pulpen"
                            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div><label class="text-sm text-gray-600 font-medium">Kategori</label><select name="kategori_id"
                            required
                            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategori as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-4">
                    <div><label class="text-sm text-gray-600 font-medium">Harga Beli</label><input type="number"
                            name="harga_beli" required min="0"
                            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="text-sm text-gray-600 font-medium">Qty</label><input type="number"
                                name="qty" required min="0"
                                class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div><label class="text-sm text-gray-600 font-medium">Satuan</label><input type="text"
                                name="satuan" required placeholder="pcs, box"
                                class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    <div><label class="text-sm text-gray-600 font-medium">Total Harga</label><input type="number"
                            name="total_harga" id="total_harga" readonly
                            class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2"></div>
                    <div><label class="text-sm text-gray-600 font-medium">Keterangan</label>
                        <textarea name="keterangan" rows="2"
                            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Opsional..."></textarea>
                    </div>
                </div>
                <div class="col-span-1 md:col-span-2 flex justify-end gap-3 pt-3 border-t"><button type="button"
                        onclick="tambahBarangModalClose()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium px-5 py-2 rounded-lg">Batal</button><button
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <!-- MODAL EDIT -->
    <div class="fixed inset-0 flex items-center justify-center z-50 hidden" id="barangModal">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl p-6 relative z-10">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-xl font-semibold text-green-700">Edit Barang</h3>
            </div>
            <form method="POST" id="formBarangModal" class="grid grid-cols-1 md:grid-cols-2 gap-6">@csrf<input
                    type="hidden" name="_method" value="PATCH">
                <div class="space-y-4">
                    <div><label class="text-sm font-medium text-gray-600">Kode Barang</label><input type="text"
                            id="edit_kode_barang" name="kode_barang" readonly
                            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div><label class="text-sm font-medium text-gray-600">Tanggal Masuk</label><input type="date"
                            name="tanggal_masuk" id="edit_tanggal_masuk" required
                            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div><label class="text-sm font-medium text-gray-600">Nama Barang</label><input type="text"
                            name="nama_barang" id="edit_nama_barang" required
                            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div><label class="text-sm font-medium text-gray-600">Kategori</label><select name="kategori_id"
                            id="edit_kategori_id" required
                            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            @foreach ($kategori as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select></div>
                </div>
                <div class="space-y-4">
                    <div><label class="text-sm font-medium text-gray-600">Harga Beli</label><input type="number"
                            name="harga_beli" id="edit_harga_beli" min="0" required
                            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="text-sm font-medium text-gray-600">Qty</label><input type="number"
                                name="qty" id="edit_qty" min="0" required
                                class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div><label class="text-sm font-medium text-gray-600">Satuan</label><input type="text"
                                name="satuan" id="edit_satuan" required
                                class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    <div><label class="text-sm font-medium text-gray-600">Total Harga</label><input type="number"
                            name="total_harga" id="edit_total_harga" readonly
                            class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2"></div>
                    <div><label class="text-sm font-medium text-gray-600">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" rows="2"
                            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"></textarea>
                    </div>
                </div>
                <div class="col-span-1 md:col-span-2 flex justify-end gap-3 pt-3 border-t"><button type="button"
                        onclick="barangModalClose()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium px-5 py-2 rounded-lg">Batal</button><button
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-lg">Simpan
                        Perubahan</button></div>
            </form>
        </div>
    </div>

    <!-- SCRIPT -->
    <script>
        // Modal functions
        function tambahBarangModal() {
            document.getElementById('tambahBarangModal').classList.remove('hidden');
        }

        function tambahBarangModalClose() {
            document.getElementById('tambahBarangModal').classList.add('hidden');
        }

        function barangModalClose() {
            document.getElementById('barangModal').classList.add('hidden');
        }

        // Auto-calculate total for Tambah form
        const hargaInput = document.querySelector("input[name='harga_beli']");
        const qtyInput = document.querySelector("input[name='qty']");
        const totalInput = document.getElementById("total_harga");

        function hitungTotal() {
            totalInput.value = (parseInt(hargaInput.value) || 0) * (parseInt(qtyInput.value) || 0);
        }
        if (hargaInput) hargaInput.addEventListener("input", hitungTotal);
        if (qtyInput) qtyInput.addEventListener("input", hitungTotal);

        // Auto-calculate total for Edit form
        const editHarga = document.getElementById("edit_harga_beli");
        const editQty = document.getElementById("edit_qty");
        const editTotal = document.getElementById("edit_total_harga");

        function hitungEditTotal() {
            editTotal.value = (parseInt(editHarga.value) || 0) * (parseInt(editQty.value) || 0);
        }
        if (editHarga) editHarga.addEventListener("input", hitungEditTotal);
        if (editQty) editQty.addEventListener("input", hitungEditTotal);

        // Edit Modal function
        function editBarangModal(button) {
            const id = button.dataset.id;
            const form = document.getElementById('formBarangModal');
            form.setAttribute('action', "{{ route('barang.update', ':id') }}".replace(':id', id));

            // Populate form fields
            document.getElementById('edit_kode_barang').value = button.dataset.kode;
            document.getElementById('edit_tanggal_masuk').value = button.dataset.tanggal_masuk;
            document.getElementById('edit_nama_barang').value = button.dataset.nama;
            document.getElementById('edit_kategori_id').value = button.dataset.kategori;
            document.getElementById('edit_harga_beli').value = button.dataset.harga;
            document.getElementById('edit_qty').value = button.dataset.qty;
            document.getElementById('edit_satuan').value = button.dataset.satuan;
            document.getElementById('edit_total_harga').value = button.dataset.total;
            document.getElementById('edit_keterangan').value = button.dataset.keterangan ?? '';

            document.getElementById('barangModal').classList.remove('hidden');
        }

        {{-- PERUBAHAN: Hapus JavaScript filter lama yang tidak perlu --}}
    </script>
</x-app-layout>
