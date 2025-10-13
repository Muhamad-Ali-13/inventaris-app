<?php
use App\Models\Barang;
use Illuminate\Support\Facades\Route;


Route::get('/barang/by-kategori/{id}', function ($id) {
    return response()->json(
        Barang::where('kategori_id', $id)->get(['id', 'nama_barang'])
    );
});


