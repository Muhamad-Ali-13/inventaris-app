<?php

namespace App\Providers;

use App\Models\Karyawan;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
            return $user->role === 'Admin';
        });

        Gate::define('role-D', function ($user) {
            return $user->role === 'Direktur';
        });

        Gate::define('role-K', function ($user) {
            return $user->role === 'Karyawan';
        }); 

        Gate::define('access-laporan', function ($user) {
            return in_array($user->role, ['Admin', 'Direktur']); // Admin dan Direktur bisa akses
        });


        if (Schema::hasTable('users')) {
            view()->share('jumlahKaryawan', User::count());
        } else {
            view()->share('jumlahKaryawan', 0);
        }

        if (Schema::hasTable('barangs')) {
            view()->share('jumlahBarang', Barang::count());
        } else {
            view()->share('jumlahBarang', 0);
        }

        if (Schema::hasTable('transaksis')) {
            view()->share('jumlahTransaksi', Transaksi::count());
        } else {
            view()->share('jumlahTransaksi', 0);
        }

        view()->share('bulanLabels', ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']);
        view()->share('dataPemasukan', []);
        view()->share('dataPengeluaran', []);
    }
}
