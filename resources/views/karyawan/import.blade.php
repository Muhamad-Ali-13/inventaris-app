<x-app-layout>
    <div class="py-10 bg-gradient-to-br from-gray-50 via-white to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Import Data Karyawan</h3>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-blue-800 mb-2">Petunjuk Import Data:</h4>
                    <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                        <li>File harus berformat .xlsx, .xls, atau .csv</li>
                        <li>File harus memiliki header: NIP, Nama Lengkap, Email, Departemen, No Telp, Alamat, Tanggal Masuk, Role, Password (opsional)</li>
                        <li>Jika email kosong, sistem akan otomatis membuat email berdasarkan nama</li>
                        <li>Jika password kosong, password default adalah "password123"</li>
                        <li>Role yang valid: Admin, Direktur, Karyawan</li>
                    </ul>
                </div>

                <div class="mb-6">
                    <a href="{{ route('karyawans.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow text-sm font-semibold inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template Excel
                    </a>
                </div>

                <form action="{{ route('karyawans.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-6">
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel</label>
                        <input type="file" name="file" id="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" required>
                        @error('file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('karyawans.index') }}" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white shadow-sm transition">
                            Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>