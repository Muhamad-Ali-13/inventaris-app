<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';

    protected $fillable = [
        'user_id',
        'departemen_id',
        'tipe',
        'status',
        'deskripsi',
        'tanggal_pengajuan',
        'tanggal_approval',
    ];

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
        return $this->hasMany(TransaksiDetail::class);
    }
}
