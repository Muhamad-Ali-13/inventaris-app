<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'departemen_id',
        'jenis',
        'status',
        'tanggal_pengajuan',
        'tanggal_disetujui',
        'keterangan',
    ];

    public static function generateKode($jenis)
    {
        $prefix = $jenis === 'pemasukan' ? 'PMK' : 'PLR';
        $last = self::where('jenis', $jenis)->latest()->first();
        $next = $last ? ((int) substr($last->kode_transaksi, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class, 'kode_transaksi', 'kode_transaksi');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
