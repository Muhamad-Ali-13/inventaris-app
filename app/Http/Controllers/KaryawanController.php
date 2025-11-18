<?php

namespace App\Http\Controllers;

use App\Exports\KaryawanExport;
use App\Imports\KaryawanImport;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class KaryawanController extends Controller
{
    /**
     * Tampilkan daftar karyawan (index view sudah menunggu $karyawans dan $departemen)
     */
    public function index(Request $request)
    {
        // Ambil nilai 'entries' dari request, default 10
        $entries = $request->get('entries', 10);
        // Ambil nilai 'search' dari request
        $search = $request->get('search');

        // Query dasar untuk mengambil karyawan beserta relasinya
        $query = Karyawan::with(['user', 'departemen']);

        // Jika ada parameter search, tambahkan filter ke query
        if ($search) {
            $query->where('nama_lengkap', 'like', '%' . $search . '%')
                ->orWhereHas('departemen', function ($q) use ($search) {
                    $q->where('nama_departemen', 'like', '%' . $search . '%');
                });
        }

        // Jalankan query dengan pagination, dan urutkan dari yang terbaru
        $karyawans = $query->latest()->paginate($entries);

        // Ambil semua data departemen (diperlukan untuk modal)
        $departemen = Departemen::all();

        // Tambahkan parameter query string ke link pagination
        // Ini agar saat pindah halaman, filter search dan entries tetap terbawa
        $karyawans->appends($request->query());

        return view('karyawan.index', compact('karyawans', 'departemen'));
    }
    public function export()
    {
        return Excel::download(new KaryawanExport, 'data-karyawan-' . date('Y-m-d') . '.xlsx');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new KaryawanImport, $request->file('file'));

            return redirect()->route('karyawans.index')->with('success', 'Data karyawan berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris ke-" . $failure->row() . ": " . implode(', ', $failure->errors());
            }

            return redirect()->route('karyawans.index')->with('error', 'Import gagal. ' . implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            return redirect()->route('karyawans.index')->with('error', 'Terjadi kesalahan saat mengimport data. Pastikan format file sudah benar.');
        }
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
