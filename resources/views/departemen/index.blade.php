<x-app-layout>

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
                            + Tambah Departemen
                        </button>
                    @endcan
                </div>

                <!-- SEARCH + ENTRIES -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            Tampilkan
                            <select id="entries"
                                class="border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            entri
                        </label>
                    </div>

                    <div class="relative w-full sm:w-64">
                        <input type="text" id="searchInput" placeholder="Cari departemen..."
                            class="w-full border border-gray-300 rounded-lg pl-10 text-sm focus:ring-green-500 focus:border-green-500">
                        <i class="fi fi-rr-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>

                <!-- DESKTOP TABLE -->
                <div class="overflow-x-auto rounded-lg border border-gray-100 hidden md:block">
                    <table class="w-full text-sm text-left text-gray-700" id="departemenTable">
                        <thead class="bg-green-100 text-green-800 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">No</th>
                                <th class="px-6 py-3">Nama Departemen</th>
                                @can('role-A')
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departemen as $index => $d)
                                <tr class="border-b hover:bg-green-50 transition">
                                    <td class="px-6 py-3">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $d->nama_departemen }}</td>
                                    @can('role-A')
                                        <td class="px-6 py-3 text-center flex justify-center gap-2">
                                            <button class="text-blue-600 hover:text-blue-800"
                                                onclick="editDepartemenModal(this)" data-id="{{ $d->id }}"
                                                data-nama="{{ $d->nama_departemen }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 4h2m2 0h2a2 2 0 012 2v2m0 0v2m0-2h2m-2 0h-2m-2 0h-2m0 0V4m0 4H7m0 0H5m0 0H3m0 0V6a2 2 0 012-2h2m0 0h2m0 0v2" />
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
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD VIEW -->
                <div class="md:hidden space-y-4" id="departemenCards">
                    @foreach ($departemen as $d)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-lg text-gray-800">{{ $d->nama_departemen }}</h4>
                            </div>
                            @can('role-A')
                                <div class="flex gap-2 mt-4">
                                    <button class="text-blue-600 hover:text-blue-800" onclick="editDepartemenModal(this)"
                                        data-id="{{ $d->id }}" data-nama="{{ $d->nama_departemen }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 4h2m2 0h2a2 2 0 012 2v2m0 0v2m0-2h2m-2 0h-2m-2 0h-2m0 0V4m0 4H7m0 0H5m0 0H3m0 0V6a2 2 0 012-2h2m0 0h2m0 0v2" />
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
                    @endforeach
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
            const url = "{{ route('departemen.update', ':id') }}".replace(':id', id);

            // Ambil token CSRF dari meta (fallback jika tidak ada)
            const meta = document.querySelector('meta[name="csrf-token"]');
            const token = meta ? meta.getAttribute('content') : '';

            // Buat HTML modal dynamic
            const html = `
<div id="departemenModal" class="fixed inset-0 flex items-center justify-center z-50">
    <div class="fixed inset-0 bg-black bg-opacity-40" onclick="closeModal('departemenModal')"></div>
    <div class="relative bg-white rounded-xl shadow-lg w-full max-w-md mx-5 p-6 border border-green-200">
        <div class="flex items-center justify-between border-b pb-3 mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Edit Departemen</h3>
            <button onclick="closeModal('departemenModal')" class="text-gray-500 hover:text-gray-800 text-xl">&times;</button>
        </div>

        <form id="formEditDepartemen">
            <input type="hidden" name="_token" value="${token}">
            <input type="hidden" name="_method" value="PATCH">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Departemen</label>
                <input type="text" name="nama_departemen" value="${escapeHtml(nama)}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                    required>
                <p id="editErrors" class="mt-2 text-sm text-red-600 hidden"></p>
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

            // Utility: escape string untuk value input (prevent breaking HTML)
            function escapeHtml(unsafe) {
                return ('' + unsafe)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // Setelah form dimasukkan ke DOM, pasang event listener
            const form = document.getElementById('formEditDepartemen');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(form);
                // Pastikan method PATCH dikirim (safeguard)
                formData.set('_method', 'PATCH');

                try {
                    const response = await fetch(url, {
                        method: 'POST', // menggunakan POST + _method=PATCH
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest', // supaya Laravel deteksi ajax
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    // Jika response JSON
                    const contentType = response.headers.get('content-type') || '';
                    if (contentType.includes('application/json')) {
                        const data = await response.json();

                        if (response.ok && data.success) {
                            // Update tampilan tabel (desktop)
                            const editBtn = document.querySelector(`button[data-id='${id}']`);
                            if (editBtn) {
                                // Jika desktop, cari row tr terdekat
                                const row = editBtn.closest('tr');
                                if (row) {
                                    row.querySelector('td:nth-child(2)').textContent = data.nama_departemen;
                                }
                                // Update atribut data-nama agar modal berikutnya berisi nama yang benar
                                editBtn.setAttribute('data-nama', data.nama_departemen);
                            }

                            // Update mobile card (jika ada)
                            const cardButtons = Array.from(document.querySelectorAll(
                                `#departemenCards [data-id='${id}']`));
                            cardButtons.forEach(btn => {
                                const card = btn.closest('div'); // kartu terdekat
                                if (card) {
                                    const title = card.querySelector('h4');
                                    if (title) title.textContent = data.nama_departemen;
                                }
                                btn.setAttribute('data-nama', data.nama_departemen);
                            });

                            // Tutup modal dan bersihkan container
                            closeModal('departemenModal');
                            // Hapus HTML modal setelah animasi (opsional): langsung kosongkan
                            document.getElementById('departemenModalContainer').innerHTML = '';
                        } else {
                            // Menampilkan pesan error validasi jika ada
                            const errEl = document.getElementById('editErrors');
                            errEl.classList.remove('hidden');
                            if (data.errors) {
                                // gabungkan pesan validasi
                                const messages = [];
                                for (const k in data.errors) {
                                    messages.push(...data.errors[k]);
                                }
                                errEl.textContent = messages.join(' • ');
                            } else if (data.message) {
                                errEl.textContent = data.message;
                            } else {
                                errEl.textContent = 'Gagal memperbarui data.';
                            }
                        }
                    } else {
                        // Jika server mengembalikan redirect/HTML (misal validasi gagal tanpa JSON)
                        const text = await response.text();
                        console.error('Unexpected non-JSON response:', text);
                        alert('Gagal memperbarui data. Periksa input atau lihat console untuk detail.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Terjadi kesalahan saat mengirim data. Coba lagi.');
                }
            });
        }

        // SEARCH & ENTRIES (desktop & mobile)
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const entriesSelect = document.getElementById('entries');
            const table = document.getElementById('departemenTable');
            const tbody = table ? table.querySelector('tbody') : null;
            const rows = tbody ? Array.from(tbody.getElementsByTagName('tr')) : [];

            const cardsContainer = document.getElementById('departemenCards');
            const cards = cardsContainer ? Array.from(cardsContainer.children) : [];

            function filterTable() {
                const query = (searchInput.value || '').toLowerCase();
                const limit = parseInt(entriesSelect.value || 10);
                let visibleCount = 0;

                // TABLE
                rows.forEach(row => {
                    const name = (row.cells[1].textContent || '').toLowerCase();
                    const match = name.includes(query);
                    if (match && visibleCount < limit) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // CARDS
                visibleCount = 0;
                cards.forEach(card => {
                    const titleEl = card.querySelector('h4');
                    const name = titleEl ? titleEl.textContent.toLowerCase() : '';
                    const match = name.includes(query);
                    if (match && visibleCount < limit) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            if (searchInput) searchInput.addEventListener('keyup', filterTable);
            if (entriesSelect) entriesSelect.addEventListener('change', filterTable);
            filterTable();
        });
    </script>

</x-app-layout>
