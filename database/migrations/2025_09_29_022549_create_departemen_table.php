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
        Schema::create('departemen', function (Blueprint $table) {
            $table->id();     // opsional, tapi bagus
            $table->string('nama_departemen')->unique();      // agar tidak duplikat
            $table->text('deskripsi')->nullable();

            $table->timestamps();
            $table->index('nama_departemen');                 // mempercepat query
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departemen');
    }
};
