<?php

namespace App\Providers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('role-A', function ($user) {
            return $user->role === 'A';
        });

        view()->share('jumlahKaryawan', User::count());
        view()->share('jumlahBarang', Barang::count());
        view()->share('jumlahTransaksi', Transaksi::count());
        view()->share('bulanLabels', ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']);
        view()->share('dataPemasukan', []);
        view()->share('dataPengeluaran', []);
    }
}
