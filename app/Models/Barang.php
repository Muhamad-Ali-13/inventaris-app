<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $fillable = ['nama_barang', 'kategori_id', 'stok', 'satuan', 'keterangan'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class, 'barang_id');
    }
}
