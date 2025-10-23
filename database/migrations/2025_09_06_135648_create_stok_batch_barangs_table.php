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
        Schema::create('stok_batch_barang', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('id_barang')->constrained('barang')->onDelete('cascade');
            $table->decimal('jumlah', 10, 2);
            // $table->foreignUlid('id_satuan')->nullable()->constrained('satuan')->nullOnDelete();
            $table->date('tgl_expired')->nullable();
            $table->bigInteger('harga_beli')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_batch_barang');
    }
};