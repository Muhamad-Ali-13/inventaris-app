<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('departemen_id')->constrained('departemen');
            $table->enum('tipe', ['pengeluaran', 'pemasukan']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->dateTime('tanggal_pengajuan');
            $table->dateTime('tanggal_approval')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
