<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kategori') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="gap-5 items-start flex">
                <!-- FORM INPUT -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg w-1/2 p-4">
                    <div class="p-4 bg-gray-100 mb-2 rounded-xl font-bold">
                        FORM INPUT KATEGORI
                    </div>
                    <form class="max-w-sm mx-auto" method="POST" action="{{ route('kategori.store') }}">
                        @csrf
                        <div class="mb-5">
                            <label for="nama_kategori"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Nama Kategori
                            </label>
                            <input type="text" name="nama_kategori" id="nama_kategori"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 
                                       dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                                       dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required placeholder="Contoh: ATK, ETK" />
                        </div>
                        <button type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none 
                                   focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto 
                                   px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 
                                   dark:focus:ring-blue-800">
                            Simpan
                        </button>
                    </form>
                </div>

                <!-- TABEL DATA -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg w-full p-4">
                    <div class="p-4 bg-gray-100 mb-2 rounded-xl font-bold">
                        DATA KATEGORI
                    </div>
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 bg-gray-100">NO</th>
                                    <th scope="col" class="px-6 py-3">NAMA KATEGORI</th>
                                    <th scope="col" class="px-6 py-3">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($kategori as $k)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white bg-gray-100">
                                            {{ $no++ }}
                                        </th>
                                        <td class="px-6 py-4 bg-gray-100">{{ $k->nama_kategori }}</td>
                                        <td class="px-6 py-4">
                                            <button type="button"
                                                class="bg-amber-400 p-3 w-10 h-10 rounded-xl text-white hover:bg-amber-500"
                                                onclick="editKategoriModal(this)" data-modal-target="kategoriModal"
                                                data-id="{{ $k->id }}" data-nama="{{ $k->nama_kategori }}">
                                                <i class="fi fi-sr-file-edit"></i>
                                            </button>
                                            <form action="{{ route('kategori.destroy', $k->id) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 p-3 w-10 h-10 rounded-xl text-white hover:bg-red-600">
                                                    <i class="fi fi-sr-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="fixed inset-0 flex items-center justify-center z-50 hidden" id="kategoriModal">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="fixed inset-0 flex items-center justify-center">
            <div class="w-full md:w-1/2 relative bg-white rounded-lg shadow mx-5">
                <div class="flex items-start justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Update Kategori</h3>
                    <button type="button" onclick="kategoriModalClose(this)" data-modal-target="kategoriModal"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg 
                               text-sm w-8 h-8 ml-auto inline-flex justify-center items-center">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form method="POST" id="formKategoriModal">
                    @csrf
                    <div class="flex flex-col p-4 space-y-6">
                        <div>
                            <label for="nama_kategori"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                                Kategori</label>
                            <input type="text" name="nama_kategori" id="edit_nama_kategori"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 
                                       dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                                       dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required />
                        </div>
                    </div>
                    <div class="flex items-center p-4 space-x-2 border-t border-gray-200 rounded-b">
                        <button type="submit" id="formKategoriButton"
                            class="bg-green-400 m-2 w-40 h-10 rounded-xl hover:bg-green-500">Simpan</button>
                        <button type="button" data-modal-target="kategoriModal" onclick="kategoriModalClose(this)"
                            class="bg-red-500 m-2 w-40 h-10 rounded-xl text-white hover:bg-red-600">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const editKategoriModal = (button) => {
            const id = button.dataset.id;
            const nama = button.dataset.nama;

            let url = "{{ route('kategori.update', ':id') }}".replace(':id', id);

            document.getElementById('edit_nama_kategori').value = nama;
            document.getElementById('formKategoriModal').setAttribute('action', url);

            // tambahkan method PATCH
            let methodInput = document.createElement('input');
            methodInput.setAttribute('type', 'hidden');
            methodInput.setAttribute('name', '_method');
            methodInput.setAttribute('value', 'PATCH');

            const form = document.getElementById('formKategoriModal');
            if (!form.querySelector('input[name="_method"]')) {
                form.appendChild(methodInput);
            }

            // tampilkan modal
            document.getElementById('kategoriModal').classList.toggle('hidden');
        }

        const kategoriModalClose = (button) => {
            const modalTarget = button.dataset.modalTarget;
            document.getElementById(modalTarget).classList.toggle('hidden');
        }
    </script>
</x-app-layout>
