<?php

use App\Http\Controllers\{
    AkuntansiController,
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

// Login
Route::get('/', fn() => view('auth.login'));

// Dashboard (login & email verified)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Semua route hanya untuk user login
Route::middleware(['auth', 'throttle:60,1'])->group(function () {

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ----------------------
    // Admin Only
    // ----------------------
    Route::middleware('can:role-A')->group(function () {
        // Master Data
        // Route::resource('karyawans', KaryawanController::class);
        Route::resource('departemen', DepartemenController::class);
        Route::resource('kategori', KategoriController::class);
        Route::resource('barang', BarangController::class);

        Route::resource('karyawans', KaryawanController::class)->only([
            'index',
            'store',
            'update',
            'destroy'
        ]);

        // Tambahkan route spesifik untuk export dan import DI BAWAH route resource
        Route::get('karyawans/export', [KaryawanController::class, 'export'])->name('karyawans.export');
        Route::post('karyawans/import', [KaryawanController::class, 'import'])->name('karyawans.import');

        // Pemasukan
        Route::controller(PemasukanController::class)->group(function () {
            Route::get('/pemasukan', 'index')->name('pemasukan.index');
            Route::post('/pemasukan/store', 'store')->name('pemasukan.store');
            Route::put('/pemasukan/{id}', 'update')->name('pemasukan.update');
            Route::post('/pemasukan/{id}/approve', 'approve')->name('pemasukan.approve');
            Route::post('/pemasukan/{id}/reject', 'reject')->name('pemasukan.reject');
            Route::delete('/pemasukan/{id}', 'destroy')->name('pemasukan.destroy');
        });

        Route::controller(PengeluaranController::class)->group(function () {
            Route::get('/pengeluaran', 'index')->name('pengeluaran.index');
            Route::post('/pengeluaran/store', 'store')->name('pengeluaran.store');
            Route::put('/pengeluaran/{id}', [PengeluaranController::class, 'update'])->name('pengeluaran.update');
            Route::post('/pengeluaran/{id}/approve', 'approve')->name('pengeluaran.approve');
            Route::post('/pengeluaran/{id}/reject', 'reject')->name('pengeluaran.reject');
            Route::get('/pengeluaran/{id}/edit-data', 'getEditData');
            Route::delete('/pengeluaran/{id}', 'destroy')->name('pengeluaran.destroy');
        });

        // Log aktivitas
        Route::get('log-aktivitas', [LogAktivitasController::class, 'index'])->name('log.index');
    });

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

    // ----------------------
    // Admin + Direktur
    // ----------------------
    Route::middleware('can:access-laporan')->group(function () {
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
        Route::get('laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');
        Route::get('akuntansi', [AkuntansiController::class, 'index'])->name('akuntansi.index');
        Route::get('akuntansi/akuntansipdf', [AkuntansiController::class, 'exportPdf'])->name('akuntansi.exportPdf');
        Route::get('akuntansi/excel', [AkuntansiController::class, 'exportExcel'])->name('akuntansi.exportExcel');
    });

    // ----------------------
    // Semua role (Admin, Direktur, Karyawan)
    // ----------------------
    Route::get('/stok-barang', [BarangController::class, 'stok'])->name('stok.barang');

    Route::resource('transaksi', TransaksiController::class);
    Route::resource('transaksidetail', TransaksiDetailController::class);
    Route::post('transaksi/{id}/approve', [TransaksiController::class, 'approve'])->name('transaksi.approve');
    Route::post('transaksi/{id}/reject', [TransaksiController::class, 'reject'])->name('transaksi.reject');

    // Barang per kategori
    Route::get('/barang/by-kategori/{id}', [BarangController::class, 'getByKategori']);
});

require __DIR__ . '/auth.php';
