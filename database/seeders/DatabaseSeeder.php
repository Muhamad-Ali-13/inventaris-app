<?php

namespace Database\Seeders;

use App\Models\User;
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
