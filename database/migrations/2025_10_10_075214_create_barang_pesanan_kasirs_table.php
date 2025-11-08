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
        Schema::create('barang_pesanan_kasir', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('id_pesanan_kasir')->constrained('pesanan_kasir')->onDelete('cascade');
            $table->foreignUlid('id_barang')->constrained('barang')->onDelete('cascade');
            $table->smallInteger('jumlah');
            $table->foreignUlid('id_satuan')->constrained('satuan')->onDelete('cascade');
            $table->mediumInteger('diskon')->default(0);
            // $table->foreignUlid('id_promo_barang')->nullable()->constrained('promo_barang')->nullOnDelete();
            $table->unsignedMediumInteger('harga');
            $table->unsignedMediumInteger('harga_satuan_utama');
            $table->integer('sub_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_pesanan_kasir');
    }
};
