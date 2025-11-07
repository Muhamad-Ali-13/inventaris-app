<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    /**
     * Tampilkan daftar karyawan (index view sudah menunggu $karyawans dan $departemen)
     */
    public function index()
    {
        $departemen = Departemen::all();
        $karyawans = Karyawan::with(['user', 'departemen'])->latest()->paginate(10);

        return view('karyawan.index', compact('karyawans', 'departemen'));
    }

    /**
     * Simpan karyawan baru + buat user (store)
     * Sesuai form di index.blade.php: form create mengirim nip, nama_lengkap, departemen_id, no_telp, alamat, tanggal_masuk, email, password
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip'            => 'nullable|string|max:50|unique:karyawans,nip',
            'nama_lengkap'   => 'required|string|max:255',
            'departemen_id'  => 'nullable|exists:departemen,id',
            'no_telp'        => 'nullable|string|max:20',
            'alamat'         => 'nullable|string',
            'tanggal_masuk'  => 'nullable|date',

            // user fields (create form provides email & password)
            'email'          => 'required|email|max:255|unique:users,email',
            'password'       => 'required|string|min:6',
            // role tidak dikirim oleh form -> beri default 'U'
        ]);

        DB::beginTransaction();

        try {
            // 1) Buat user dulu
            $user = User::create([
                'name'     => $validated['nama_lengkap'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'Karyawan', // default role user (sesuaikan jika sistem Anda memakai 'karyawan' atau 'U')
            ]);

            // 2) Buat karyawan terkait
            Karyawan::create([
                'user_id'        => $user->id,
                'nip'            => $validated['nip'] ?? null,
                'nama_lengkap'   => $validated['nama_lengkap'],
                'departemen_id'  => $validated['departemen_id'] ?? null,
                'no_telp'        => $validated['no_telp'] ?? null,
                'alamat'         => $validated['alamat'] ?? null,
                'tanggal_masuk'  => $validated['tanggal_masuk'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('karyawans.index')->with('success', 'Karyawan & User berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            // jangan tampilkan exception di production — ini untuk debugging sementara
            return redirect()->back()->withInput()->withErrors(['general' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }


    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $validated = $request->validate([
            'nip'            => ['nullable', 'string', 'max:50', Rule::unique('karyawans', 'nip')->ignore($karyawan->id)],
            'nama_lengkap'   => 'required|string|max:255',
            'departemen_id'  => 'nullable|exists:departemen,id',
            'no_telp'        => 'nullable|string|max:20',
            'alamat'         => 'nullable|string',
            'tanggal_masuk'  => 'nullable|date',
            'role'           => 'required|in:Admin,Direktur,Karyawan',
        ]);

        // Update karyawan
        $karyawan->update([
            'nip'            => $validated['nip'] ?? null,
            'nama_lengkap'   => $validated['nama_lengkap'],
            'departemen_id'  => $validated['departemen_id'] ?? null,
            'no_telp'        => $validated['no_telp'] ?? null,
            'alamat'         => $validated['alamat'] ?? null,
            'tanggal_masuk'  => $validated['tanggal_masuk'] ?? null,
            'role'           => $validated['role'],
        ]);

        // Update role di tabel users
        if ($karyawan->user) { // pastikan ada relasi user
            $karyawan->user->update([
                'role' => $validated['role'],
            ]);
        }

        return redirect()->route('karyawans.index')->with('success', 'Karyawan berhasil diperbarui.');
    }


    /**
     * Hapus karyawan (beserta user terkait jika ada)
     */
    public function destroy($id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);

        DB::transaction(function () use ($karyawan) {
            // Jika ada user terkait, hapus user -> jika ada FK cascade, karyawan akan ikut terhapus
            if ($karyawan->user) {
                $user = $karyawan->user;
                $user->delete();
            }

            // Jika karyawan masih ada (mis. FK tidak cascade), hapus manual
            if (Karyawan::where('id', $karyawan->id)->exists()) {
                $karyawan->delete();
            }
        });

        return redirect()->route('karyawans.index')->with('success', 'Karyawan & User berhasil dihapus.');
    }
}
