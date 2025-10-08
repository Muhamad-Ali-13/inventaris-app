<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawans';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'departemen_id',
        'no_telp',
        'alamat',
        'tanggal_masuk',
    ];

    /**
     * Relasi ke User (1 karyawan punya 1 akun user)
     */
    
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Relasi ke Departemen
     */
    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    /**
     * Relasi ke Transaksi (jika ingin hubungkan transaksi ke karyawan)
     */
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'karyawan_id');
    }
}
