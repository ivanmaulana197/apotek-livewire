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
        Schema::create('detail_faktur_barang', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('id_history_barang')->constrained('history_barang')->onDelete('cascade');
            $table->foreignUlid('id_stok_batch_barang')->nullable()->constrained('stok_batch_barang')->nullOnDelete();
            $table->bigInteger('harga_beli')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_faktur_barang');
    }
};