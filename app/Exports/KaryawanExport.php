<?php

namespace App\Exports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Karyawan::with(['user', 'departemen'])->get();
    }

    public function headings(): array
    {
        return [
            'nip',
            'nama_lengkap',
            'email',
            'password', // Tambahkan password sebagai contoh, bisa dihapus nanti
            'nama_departemen',
            'no_telp',
            'alamat',
            'tanggal_masuk',
            'role'
        ];
    }

    public function map($karyawan): array
    {
        return [
            $karyawan->nip,
            $karyawan->nama_lengkap,
            $karyawan->user ? $karyawan->user->email : '',
            '', // Kolom password kosong untuk diisi saat import
            $karyawan->departemen ? $karyawan->departemen->nama_departemen : '',
            $karyawan->no_telp,
            $karyawan->alamat,
            $karyawan->tanggal_masuk,
            $karyawan->user ? $karyawan->user->role : '',
        ];
    }
}