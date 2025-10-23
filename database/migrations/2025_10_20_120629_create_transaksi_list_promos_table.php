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
        Schema::create('transaksi_list_promo', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('id_transaksi')->constrained('transaksi')->onDelete('cascade');
            $table->enum('tipe_promo', ['BARANG BONUS', 'BARANG DISKON', 'TRANSAKSI'])->default('TRANSAKSI');
            $table->foreignUlid('id_promo_barang')->nullable()->constrained('promo_barang')->onDelete('set null');
            $table->foreignUlid('id_barang')->nullable()->constrained('barang')->onDelete('cascade');
            $table->foreignUlid('id_barang_bonus')->nullable()->constrained('barang')->onDelete('cascade');
            $table->integer('jumlah')->nullable();
            // [{'tgl_expired': '2028-04-1', 'jumlah': 3}, ...]
            $table->json('tgl_expired')->nullable();
            $table->foreignUlid('id_satuan')->nullable()->constrained('satuan')->onDelete('set null');

            $table->foreignUlid('id_promo_transaksi')->nullable()->constrained('promo_transaksi')->onDelete('set null');
            $table->integer('nominal_promo')->nullable();
            $table->text('deskripsi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_list_promo');
    }
};
