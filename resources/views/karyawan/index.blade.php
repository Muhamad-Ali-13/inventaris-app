<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Data Karyawan
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Daftar Karyawan</h3>
            <a href="{{ route('karyawans.create') }}"
               class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                + Tambah Karyawan
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-4 py-2 border">No</th>
                        <th class="px-4 py-2 border">NIP</th>
                        <th class="px-4 py-2 border">Nama Lengkap</th>
                        <th class="px-4 py-2 border">Departemen</th>
                        <th class="px-4 py-2 border">Email</th>
                        {{-- <th class="px-4 py-2 border">Role</th> --}}
                        <th class="px-4 py-2 border text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($karyawans as $karyawan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 border">{{ $karyawan->nip }}</td>
                            <td class="px-4 py-2 border">{{ $karyawan->nama_lengkap }}</td>
                            <td class="px-4 py-2 border">
                                {{ $karyawan->departemen->nama_departemen ?? '-' }}
                            </td>
                            <td class="px-4 py-2 border">{{ $karyawan->user->email ?? '-' }}</td>
                            <td class="px-4 py-2 border">
                                @if($karyawan->user && $karyawan->user->role == 'A')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-sm">Admin</span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-sm">User</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 border text-center">
                                <a href="{{ route('karyawans.edit', $karyawan->id) }}"
                                   class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                    Edit
                                </a>

                                <form action="{{ route('karyawans.destroy', $karyawan->id) }}"
                                      method="POST" class="inline-block"
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-3 border text-center text-gray-500">
                                Belum ada data karyawan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $karyawans->links() }}
        </div>
    </div>
</x-app-layout>
