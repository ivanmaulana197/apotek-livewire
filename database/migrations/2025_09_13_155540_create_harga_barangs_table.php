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
        Schema::create('harga_barang', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('id_barang')->constrained('barang')->onDelete('cascade');
            $table->decimal('jumlah_min', 10, 2);
            $table->foreignUlid('id_satuan')->nullable()->constrained('satuan')->nullOnDelete();
            // $table->enum('tipe',['UTAMA','OPSI']);
            // $table->boolean('is_default')->default(false);
            $table->enum('mode', ['PERSEN', 'HARGA'])->default('PERSEN');
            $table->decimal('persen',5,8)->default(0);
            $table->bigInteger('harga')->default(0);
            $table->bigInteger('harga_satuan_utama')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_barang');
    }
};
