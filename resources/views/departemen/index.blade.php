<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <div class="py-10 bg-gradient-to-br from-gray-50 via-white to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">

                <!-- HEADER + TOOLS -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">🏢 Data Departemen</h3>
                        <p class="text-gray-500 text-sm">Kelola departemen perusahaan secara real-time.</p>
                    </div>

                    @can('role-A')
                        <button onclick="openModal('departemenCreateModal')"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg shadow transition text-sm font-semibold">
                            + Tambah
                        </button>
                    @endcan
                </div>

                <!-- SEARCH + ENTRIES -->
                {{-- PERUBAHAN: Membungkus filter dalam form GET --}}
                <form action="{{ route('departemen.index') }}" method="GET"
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            Tampilkan
                            <select name="entries" id="entries"
                                class="border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500"
                                onchange="this.form.submit()">
                                <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            entri
                        </label>
                    </div>

                    <div class="relative w-full sm:w-64">
                        @if (request('search'))
                            <input type="hidden" name="entries" value="{{ request('entries') }}">
                        @endif
                        <input type="text" name="search" placeholder="Cari departemen..."
                            value="{{ request('search') }}"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        <i class="fi fi-rr-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </form>

                <!-- DESKTOP TABLE -->
                <div class="overflow-x-auto rounded-lg border border-gray-100 hidden md:block">
                    <table class="w-full text-sm text-left text-gray-700" id="departemenTable">
                        <thead class="bg-green-100 text-green-800 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">No</th>
                                <th class="px-6 py-3">Nama Departemen</th>
                                <th class="px-6 py-3">Deskripsi</th>
                                @can('role-A')
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($departemen as $d)
                                <tr class="border-b hover:bg-green-50 transition">
                                    <td class="px-6 py-3">{{ $departemen->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $d->nama_departemen }}</td>
                                    <td class="px-6 py-3">{{ $d->deskripsi }}</td>
                                    @can('role-A')
                                        <td class="px-6 py-3 text-center flex justify-center gap-2">
                                            <button class="text-blue-600 hover:text-blue-800"
                                                onclick="editDepartemenModal(this)" data-id="{{ $d->id }}"
                                                data-nama="{{ $d->nama_departemen }}"
                                                data-deskripsi="{{ $d->deskripsi }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" class="w-5 h-5 inline">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </button>

                                            <form action="{{ route('departemen.destroy', $d->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                                    <td colspan="@can('role-A') 4 @else 3 @endcan"
                                        class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada data departemen
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- MOBILE CARD VIEW -->
                <div class="md:hidden space-y-4" id="departemenCards">
                    @forelse ($departemen as $d)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-lg text-gray-800">{{ $d->nama_departemen }}</h4>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>Deskripsi:</strong> {{ $d->deskripsi ?? '-' }}</p>
                            </div>
                            @can('role-A')
                                <div class="flex gap-2 mt-4">
                                    <button class="text-blue-600 hover:text-blue-800" onclick="editDepartemenModal(this)"
                                        data-id="{{ $d->id }}" data-nama="{{ $d->nama_departemen }}"
                                        data-deskripsi="{{ $d->deskripsi }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-5 h-5 inline">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('departemen.destroy', $d->id) }}" method="POST"
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
                            Tidak ada data departemen
                        </div>
                    @endforelse
                </div>
                {{-- PERUBAHAN: Tambahkan Pagination --}}
                <div class="mt-6 flex justify-center">
                    {{ $departemen->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH DEPARTEMEN -->
    <div id="departemenCreateModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/40" onclick="closeModal('departemenCreateModal')"></div>
        <div class="relative bg-white rounded-xl shadow-lg w-full max-w-md mx-5 p-6 border border-green-200">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Tambah Departemen</h3>
                <button onclick="closeModal('departemenCreateModal')"
                    class="text-gray-500 hover:text-gray-800 text-xl">&times;</button>
            </div>
            <form action="{{ route('departemen.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="nama_departemen" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Departemen
                    </label>
                    <input type="text" name="nama_departemen" id="nama_departemen" placeholder="Contoh: Keuangan"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                        required>
                </div>
                <div class="mb-4">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" placeholder="Deskripsi singkat tentang departemen"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeModal('departemenCreateModal')"
                        class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT DINAMIS -->
    <div id="departemenModalContainer"></div>

    <script>
        const openModal = (id) => document.getElementById(id).classList.remove('hidden');
        const closeModal = (id) => document.getElementById(id).classList.add('hidden');

        function editDepartemenModal(button) {
            const id = button.dataset.id;
            const nama = button.dataset.nama ?? '';
            const deskripsi = button.dataset.deskripsi ?? '';
            const url = "{{ route('departemen.update', ':id') }}".replace(':id', id);

            // Buat HTML modal dynamic
            const html = `
<div id="departemenModal" class="fixed inset-0 flex items-center justify-center z-50">
    <div class="fixed inset-0 bg-black bg-opacity-40" onclick="closeModal('departemenModal')"></div>
    <div class="relative bg-white rounded-xl shadow-lg w-full max-w-md mx-5 p-6 border border-green-200">
        <div class="flex items-center justify-between border-b pb-3 mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Edit Departemen</h3>
            <button onclick="closeModal('departemenModal')" class="text-gray-500 hover:text-gray-800 text-xl">&times;</button>
        </div>

        <form id="formEditDepartemen" action="${url}" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Departemen</label>
                <input type="text" name="nama_departemen" value="${escapeHtml(nama)}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                    required>
                <p id="editErrors" class="mt-2 text-sm text-red-600 hidden"></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <!-- PERBAIKAN: Hapus atribut value dari textarea, letakkan konten di antara tag -->
                <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">${escapeHtml(deskripsi)}</textarea>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeModal('departemenModal')"
                    class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>`;

            document.getElementById('departemenModalContainer').innerHTML = html;

            // Setelah form dimasukkan ke DOM, pasang event listener
            const form = document.getElementById('formEditDepartemen');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(form);
                const token = formData.get('_token'); // Ambil token dari form

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Update tampilan tabel (desktop)
                        const editBtn = document.querySelector(`button[data-id='${id}']`);
                        if (editBtn) {
                            const row = editBtn.closest('tr');
                            if (row) {
                                row.querySelector('td:nth-child(2)').textContent = data.nama_departemen;
                                row.querySelector('td:nth-child(3)').textContent = data.deskripsi ?? '-';
                            }
                            editBtn.setAttribute('data-nama', data.nama_departemen);
                            editBtn.setAttribute('data-deskripsi', data.deskripsi ?? '');
                        }

                        // Update mobile card
                        const card = document.querySelector(`#departemenCards [data-id='${id}']`)?.closest(
                            'div');
                        if (card) {
                            card.querySelector('h4').textContent = data.nama_departemen;
                            card.querySelector('p').innerHTML =
                                `<strong>Deskripsi:</strong> ${data.deskripsi ?? '-'}`;
                        }

                        closeModal('departemenModal');
                        document.getElementById('departemenModalContainer').innerHTML = '';
                    } else {
                        const errEl = document.getElementById('editErrors');
                        errEl.classList.remove('hidden');
                        if (data.errors) {
                            const messages = [];
                            for (const k in data.errors) {
                                messages.push(...data.errors[k]);
                            }
                            errEl.textContent = messages.join(' • ');
                        } else {
                            errEl.textContent = data.message || 'Gagal memperbarui data.';
                        }
                    }
                } catch (err) {
                    console.error(err);
                    alert('Terjadi kesalahan saat mengirim data. Coba lagi.');
                }
            });
        }

        // Utility: escape string untuk value input
        function escapeHtml(unsafe) {
            return ('' + unsafe)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        {{-- PERUBAHAN: Hapus JavaScript filter lama yang tidak perlu --}}
    </script>
</x-app-layout>
