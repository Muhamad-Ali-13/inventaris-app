<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Karyawan
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow">
            @if ($errors->any())
                <div class="mb-4 text-red-600">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('karyawans.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">NIP</label>
                        <input type="text" name="nip" value="{{ old('nip') }}" class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Departemen</label>
                        <select name="departemen_id" class="w-full border rounded p-2">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemen as $d)
                                <option value="{{ $d->id }}" {{ old('departemen_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama_departemen }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">No. Telepon</label>
                        <input type="text" name="no_telp" value="{{ old('no_telp') }}" class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Alamat</label>
                        <input type="text" name="alamat" value="{{ old('alamat') }}" class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}" class="w-full border rounded p-2">
                    </div>
                </div>

                <hr class="my-6">

                <h3 class="text-lg font-semibold mb-4">Data Akun User</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Role</label>
                        <select name="role" required class="w-full border rounded p-2">
                            <option value="U" {{ old('role') == 'U' ? 'selected' : '' }}>User</option>
                            <option value="A" {{ old('role') == 'A' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Password</label>
                        <input type="password" name="password" required class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required class="w-full border rounded p-2">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <a href="{{ route('karyawans.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
