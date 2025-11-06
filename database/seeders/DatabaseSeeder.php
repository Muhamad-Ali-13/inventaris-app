<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Departemen;
use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat akun Admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('123456789'), // ganti sesuai kebutuhan
                'role' => 'A', // A = Admin
            ]
        );

        // // Buat Departemen IT
        // $departemenIT = Departemen::updateOrCreate(
        //     ['nama_departemen' => 'IT'],
        //     [
        //         'deskripsi' => 'Departemen Teknologi Informasi',
        //     ]
        // );

        // // Buat akun User untuk Karyawan
        // $userKaryawan = User::updateOrCreate(
        //     ['email' => 'karyawan@gmail.com'],
        //     [
        //         'name' => 'John Doe',
        //         'password' => Hash::make('123456789'), // ganti sesuai kebutuhan
        //         'role' => 'K', // K = Karyawan
        //     ]
        // );

        // // Buat Karyawan yang terkait dengan User dan Departemen IT
        // Karyawan::updateOrCreate(
        //     ['user_id' => $userKaryawan->id],
        //     [
        //         'departemen_id' => $departemenIT->id,
        //         'nip' => '1234567890', // NIP contoh
        //         'alamat' => 'Jl. Contoh No. 123, Jakarta',
        //         'tanggal_masuk' => now()->subMonths(6), // Tanggal masuk 6 bulan lalu
        //     ]
        // );

        // // Kalau mau buat Direktur juga sekalian
        // User::updateOrCreate(
        //     ['email' => 'direktur@inventaris.com'],
        //     [
        //         'name' => 'Direktur',
        //         'password' => Hash::make('password123'),
        //         'role' => 'D', // D = Direktur
        //     ]
        // );
    }
}
