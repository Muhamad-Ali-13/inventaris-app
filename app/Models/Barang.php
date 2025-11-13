<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $fillable = ['kode_barang', 'tanggal_masuk', 'nama_barang', 'kategori_id', 'harga_beli', 'qty','total_harga',  'satuan', 'keterangan'];


    public static function generateKode()
    {
        $last = self::orderBy('id', 'desc')->first();

        if (!$last) {
            return 'BRG001';
        }

        $number = (int) substr($last->kode_barang, 3);
        $newNumber = str_pad($number + 1, 3, '0', STR_PAD_LEFT);

        return 'BRG' . $newNumber;
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class, 'kode_barang','kode_barang');
    }
}
