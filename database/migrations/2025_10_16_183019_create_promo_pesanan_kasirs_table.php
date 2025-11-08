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
        Schema::create('promo_pesanan_kasir', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('id_pesanan_kasir')->constrained('pesanan_kasir')->onDelete('cascade');
            $table->enum('tipe_promo', ['BARANG BONUS', 'BARANG DISKON', 'TRANSAKSI'])->default('TRANSAKSI');
            $table->foreignUlid('id_promo_barang')->nullable()->constrained('promo_barang')->onDelete('set null');
            $table->smallInteger('jumlah')->nullable();
            $table->foreignUlid('id_satuan')->nullable()->constrained('satuan')->onDelete('set null');

            $table->foreignUlid('id_promo_transaksi')->nullable()->constrained('promo_transaksi')->onDelete('set null');
            $table->integer('nominal_promo')->nullable();
            $table->string('deskripsi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_pesanan_kasir');
    }
};
