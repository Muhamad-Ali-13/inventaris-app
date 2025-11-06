<?php

use App\Http\Controllers\{
    BarangController,
    DashboardController,
    DepartemenController,
    KategoriController,
    LogAktivitasController,
    PemasukanController,
    PengeluaranController,
    ProfileController,
    TransaksiController,
    TransaksiDetailController,
    LaporanController,
    KaryawanController
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// Dashboard (hanya bisa diakses user login & email terverifikasi)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Semua route di bawah ini hanya bisa diakses user login
Route::middleware(['auth', 'throttle:60,1'])->group(function () {

    // Profil user
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Manajemen data dasar

    Route::middleware('can:role-A')->group(function () {
        Route::resource('departemen', DepartemenController::class);
        Route::resource('kategori', KategoriController::class);
        Route::resource('barang', BarangController::class);

        // Laporan
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
        Route::get('laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');

        // Pemasukan
        Route::controller(PemasukanController::class)->group(function () {
            Route::get('/pemasukan', 'index')->name('pemasukan.index');
            Route::post('/pemasukan/store', 'store')->name('pemasukan.store');
            Route::put('/pemasukan/{id}', 'update')->name('pemasukan.update');
            Route::post('/pemasukan/{id}/approve', 'approve')->name('pemasukan.approve');
            Route::post('/pemasukan/{id}/reject', 'reject')->name('pemasukan.reject');
            Route::delete('/pemasukan/{id}', 'destroy')->name('pemasukan.destroy');
        });
    });
    // route untuk karyawan
    Route::get('/stok-barang', [BarangController::class, 'stok'])->name('stok.barang');

    // Transaksi
    Route::resource('transaksi', TransaksiController::class);
    Route::resource('transaksidetail', TransaksiDetailController::class);
    Route::post('transaksi/{id}/approve', [TransaksiController::class, 'approve'])->name('transaksi.approve');
    Route::post('transaksi/{id}/reject', [TransaksiController::class, 'reject'])->name('transaksi.reject');

    // Log aktivitas
    Route::get('log-aktivitas', [LogAktivitasController::class, 'index'])->name('log.index');


    // Hanya role admin (role-A) yang bisa kelola karyawan
    Route::resource('karyawans', KaryawanController::class)->middleware('can:role-A');

    // Pengeluaran
    Route::controller(PengeluaranController::class)->group(function () {
        Route::get('/pengeluaran', 'index')->name('pengeluaran.index');
        Route::post('/pengeluaran/store', 'store')->name('pengeluaran.store');
        Route::put('/pengeluaran/{id}', 'update')->name('pengeluaran.update');
        Route::post('/pengeluaran/{id}/approve', 'approve')->name('pengeluaran.approve');
        Route::post('/pengeluaran/{id}/reject', 'reject')->name('pengeluaran.reject');
        Route::get('/pengeluaran/{id}/edit-data', 'getEditData');
        Route::delete('/pengeluaran/{id}', 'destroy')->name('pengeluaran.destroy');
    });

    // Barang per kategori
    Route::get('/barang/by-kategori/{id}', [BarangController::class, 'getByKategori']);
});

require __DIR__ . '/auth.php';
