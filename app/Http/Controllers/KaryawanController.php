<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    /**
     * Menampilkan semua data karyawan
     */
    public function index()
    {
        $karyawans = Karyawan::with(['user', 'departemen'])->latest()->paginate(10);
        return view('karyawan.index', compact('karyawans'));
    }

    /**
     * Menampilkan form tambah karyawan
     */
    public function create()
    {
        $departemen = Departemen::all();
        return view('karyawan.create', compact('departemen'));
    }

    /**
     * Simpan data karyawan baru + user
     */
    public function store(Request $request)
    {
        $request->validate([
            'nip'            => 'nullable|string|unique:karyawans,nip',
            'nama_lengkap'   => 'required|string|max:255',
            'departemen_id'  => 'nullable|exists:departemen,id',
            'no_telp'        => 'nullable|string|max:20',
            'alamat'         => 'nullable|string|max:255',
            'tanggal_masuk'  => 'nullable|date',
            // user fields
            'email'          => 'required|string|email|max:255|unique:users,email',
            'password'       => 'required|string|min:6|confirmed',
            'role'           => 'required|in:A,U',
        ]);

        // buat user dulu
        $user = User::create([
            'name'     => $request->nama_lengkap,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // buat karyawan terhubung user_id
        Karyawan::create([
            'user_id'        => $user->id,
            'nip'            => $request->nip,
            'nama_lengkap'   => $request->nama_lengkap,
            'departemen_id'  => $request->departemen_id,
            'no_telp'        => $request->no_telp,
            'alamat'         => $request->alamat,
            'tanggal_masuk'  => $request->tanggal_masuk,
        ]);

        return redirect()->route('karyawans.index')->with('success', 'Karyawan & User berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit karyawan
     */
    public function edit($id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);
        $departemen = Departemen::all();
        return view('karyawan.edit', compact('karyawan', 'departemen'));
    }

    /**
     * Update data karyawan + user
     */
    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);

        $request->validate([
            'nip'            => 'nullable|string|unique:karyawans,nip,' . $karyawan->id,
            'nama_lengkap'   => 'required|string|max:255',
            'departemen_id'  => 'nullable|exists:departemen,id',
            'no_telp'        => 'nullable|string|max:20',
            'alamat'         => 'nullable|string|max:255',
            'tanggal_masuk'  => 'nullable|date',
            // user fields
            'email'          => 'required|string|email|max:255|unique:users,email,' . $karyawan->user_id,
            'password'       => 'nullable|string|min:6|confirmed',
            'role'           => 'required|in:A,U',
        ]);

        // update user
        $karyawan->user->update([
            'name'  => $request->nama_lengkap,
            'email' => $request->email,
            'role'  => $request->role,
            'password' => $request->password ? Hash::make($request->password) : $karyawan->user->password,
        ]);

        // update karyawan
        $karyawan->update([
            'nip'            => $request->nip,
            'nama_lengkap'   => $request->nama_lengkap,
            'departemen_id'  => $request->departemen_id,
            'no_telp'        => $request->no_telp,
            'alamat'         => $request->alamat,
            'tanggal_masuk'  => $request->tanggal_masuk,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan & User berhasil diperbarui.');
    }

    /**
     * Hapus data karyawan + user
     */
    public function destroy($id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);

        // hapus user otomatis cascade hapus karyawan juga kalau FK ON DELETE CASCADE
        $karyawan->user->delete();

        return redirect()->route('karyawan.index')->with('success', 'Karyawan & User berhasil dihapus.');
    }
}
