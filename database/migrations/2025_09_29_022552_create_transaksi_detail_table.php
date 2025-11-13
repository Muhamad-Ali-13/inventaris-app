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
        Schema::create('transaksi_detail', function (Blueprint $table) {
            $table->id();

            $table->string('kode_transaksi');
            $table->foreign('kode_transaksi')
                ->references('kode_transaksi')
                ->on('transaksi')
                ->onDelete('cascade');

            $table->string('kode_barang');
            $table->foreign('kode_barang')
                ->references('kode_barang')
                ->on('barang')
                ->onDelete('cascade');

            $table->integer('harga');
            $table->integer('jumlah');
            $table->integer('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::dropIfExists('transaksi_detail');
    }
};
