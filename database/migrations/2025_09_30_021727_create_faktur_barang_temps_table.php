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
        Schema::create('faktur_barang_temp', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('id_barang')->constrained('barang')->onDelete('cascade');
            $table->smallInteger('jumlah');
            $table->foreignUlid('id_satuan')->constrained('satuan')->onDelete('cascade');
            // $table->decimal('jumlah_dalam_satuan_utama',15,2); // Jumlah dalam satuan utama
            $table->date('tgl_expired');
            $table->mediumInteger('harga_beli')->nullable();
            $table->foreignUlid('id_user')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faktur_barang_temp');
    }
};
