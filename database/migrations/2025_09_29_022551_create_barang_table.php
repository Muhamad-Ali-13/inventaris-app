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
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->date('tanggal_masuk');
            $table->string('nama_barang')->index();
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade');
            $table->bigInteger('harga_beli')->default(0);
            $table->unsignedInteger('qty')->default(0);
            $table->bigInteger('total_harga')->default(0); 
            $table->enum('satuan', ['pcs', 'box', 'unit', 'kg', 'liter', 'rim']);
            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
