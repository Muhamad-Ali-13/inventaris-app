<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Departemen;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Throwable;

class KaryawanImport implements ToModel, WithHeadingRow, WithValidation
{
    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nip' => 'nullable|string|max:50|unique:karyawans,nip',
            'nama_departemen' => 'nullable|exists:departemen,nama_departemen',
            'role' => 'required|in:Admin,Direktur,Karyawan',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_departemen.exists' => 'Departemen :input tidak ditemukan.',
            'email.unique' => 'Email :input sudah terdaftar.',
            'nip.unique' => 'NIP :input sudah terdaftar.',
        ];
    }

    public function model(array $row)
    {
        // Cari departemen berdasarkan nama
        $departemen = Departemen::where('nama_departemen', $row['nama_departemen'])->first();

        // Password default jika kosong
        $password = !empty($row['password']) ? $row['password'] : 'password';

        DB::beginTransaction();
        try {
            // 1. Buat user
            $user = User::create([
                'name'     => $row['nama_lengkap'],
                'email'    => $row['email'],
                'password' => Hash::make($password),
                'role'     => $row['role'],
            ]);

            // 2. Buat karyawan
            $karyawan = Karyawan::create([
                'user_id'        => $user->id,
                'nip'            => $row['nip'],
                'nama_lengkap'   => $row['nama_lengkap'],
                'departemen_id'  => $departemen ? $departemen->id : null,
                'no_telp'        => $row['no_telp'] ?? null,
                'alamat'         => $row['alamat'] ?? null,
                'tanggal_masuk'  => $row['tanggal_masuk'] ?? null,
            ]);

            DB::commit();
            return $karyawan;
        } catch (Throwable $e) {
            DB::rollBack();
            // Lempar exception agar ditangkap oleh controller
            throw new \Exception('Gagal menyimpan data untuk ' . $row['nama_lengkap'] . '. Error: ' . $e->getMessage());
        }
    }
}