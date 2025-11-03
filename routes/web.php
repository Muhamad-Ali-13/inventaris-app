<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
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

    Route::resource('pemasukan', PemasukanController::class);
    Route::post('pemasukan/{id}/approve', [PemasukanController::class, 'approve'])->name('pemasukan.approve');
    Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan.index');
    Route::post('/pemasukan/store', [PemasukanController::class, 'store'])->name('pemasukan.store');
    Route::put('/pemasukan/{id}', [PemasukanController::class, 'update'])->name('pemasukan.update');
    Route::post('/pemasukan/{id}/approve', [PemasukanController::class, 'approve'])->name('pemasukan.approve');
    Route::delete('/pemasukan/{id}', [PemasukanController::class, 'destroy'])->name('pemasukan.destroy');
    Route::post('/pemasukan/{id}/reject', [PemasukanController::class, 'reject'])->name('pemasukan.reject');
    
    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
    Route::post('/pengeluaran/{id}/approve', [PengeluaranController::class, 'approve'])->name('pengeluaran.approve');
    Route::post('/pengeluaran/{id}/reject', [PengeluaranController::class, 'reject'])->name('pengeluaran.reject');
    Route::put('/pengeluaran/{id}', [PengeluaranController::class, 'update'])->name('pengeluaran.update');
    Route::get('/pengeluaran/{id}/edit-data', [PengeluaranController::class, 'getEditData']);
    Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');
    Route::post('/pengeluaran/store', [PengeluaranController::class, 'store'])->name('pengeluaran.store');

    Route::get('/barang/by-kategori/{id}', [BarangController::class, 'getByKategori']);
    Route::post('transaksi/{transaksi}/approve', [TransaksiController::class, 'approve'])->name('transaksi.approve');
    Route::post('transaksi/{transaksi}/reject', [TransaksiController::class, 'reject'])->name('transaksi.reject');
    Route::post('/transaksi/{id}/approve', [TransaksiController::class, 'approve'])->name('transaksi.approve');
    Route::post('/transaksi/{id}/reject', [TransaksiController::class, 'reject'])->name('transaksi.reject');
    Route::put('/transaksi/{transaksi}', 'App\Http\Controllers\TransaksiController@update')->name('transaksi.update');
});

require __DIR__ . '/auth.php';
