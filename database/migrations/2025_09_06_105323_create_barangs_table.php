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
        Schema::create('barang', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('kode')->unique()->nullable();
            $table->string('nama');
            $table->text('keterangan')->nullable();
            $table->decimal('stok',15,2)->default(0);
            $table->integer('stok_min')->default(0);
            $table->decimal('stok_real',15,2)->nullable();


            $table->foreignUlid('id_satuan_utama')->nullable()->constrained('satuan')->nullOnDelete();
            $table->bigInteger('harga_beli_default')->default(0);


            $table->enum('mode_jual_utama', ['PERSEN', 'HARGA'])->default('PERSEN');
            $table->decimal('persen_jual_utama',5,8)->default(0);
            $table->bigInteger('harga_jual_utama')->default(0);




            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
