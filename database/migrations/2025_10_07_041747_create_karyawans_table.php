<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('nip')->unique()->nullable();
            $table->string('nama_lengkap');
            // fix: arahkan ke tabel 'departemen'
            $table->foreignId('departemen_id')
                ->nullable()
                ->constrained('departemen')
                ->onDelete('set null');

            $table->string('no_telp')->nullable();
            $table->string('alamat')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
