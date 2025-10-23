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
        Schema::create('satuan_barang', function (Blueprint $table) {
            $table->foreignUlid('id_barang')->constrained('barang')->onDelete('cascade');
            $table->foreignUlid('id_satuan')->nullable()->constrained('satuan')->nullOnDelete();
            // kenapa tidak menjadi konversi saja: karena jika satuan utamanya lebih > dari satuan_alternatifnya,
            $table->integer('rasio_satuan_utama');
            $table->integer('rasio_satuan_opsi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satuan_barang');
    }
};
