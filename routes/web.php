<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TransaksiDetailController;
use App\Models\Barang;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('departemen', DepartemenController::class);
    Route::resource('kategori', KategoriController::class);
    Route::resource('barang', BarangController::class);
    Route::resource('transaksi', TransaksiController::class);
    Route::resource('transaksidetail', TransaksiDetailController::class);
    Route::get('log-aktivitas', [LogAktivitasController::class, 'index'])->name('log.index');
    Route::get('laporan', [App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/pdf', [App\Http\Controllers\LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
    Route::get('laporan/excel', [App\Http\Controllers\LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');
    Route::resource('karyawans', App\Http\Controllers\KaryawanController::class)->middleware('can:role-A');

    Route::get('/barang/by-kategori/{id}', [BarangController::class, 'getByKategori']);
    Route::post('transaksi/{transaksi}/approve', [TransaksiController::class, 'approve'])->name('transaksi.approve');
    Route::post('transaksi/{transaksi}/reject', [TransaksiController::class, 'reject'])->name('transaksi.reject');
    Route::post('/transaksi/{id}/approve', [TransaksiController::class, 'approve'])->name('transaksi.approve');
    Route::post('/transaksi/{id}/reject', [TransaksiController::class, 'reject'])->name('transaksi.reject');
    Route::put('/transaksi/{transaksi}', 'App\Http\Controllers\TransaksiController@update')->name('transaksi.update');
});

require __DIR__ . '/auth.php';
