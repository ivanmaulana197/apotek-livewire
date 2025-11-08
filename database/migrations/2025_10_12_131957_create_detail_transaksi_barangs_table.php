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
        Schema::create('detail_transaksi_barang', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('id_history_barang')->constrained('history_barang')->onDelete('cascade');
            $table->foreignUlid('id_transaksi')->constrained('transaksi')->onDelete('cascade');
            $table->integer('harga_beli');
            $table->integer('komisi');

            $table->integer('harga');              //harga jual berdasarkan harga jual utama
            $table->integer('harga_satuan_utama'); //harga jual berdasarkan harga grosir terpilih
            $table->integer('total_harga');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi_barang');
    }
};
