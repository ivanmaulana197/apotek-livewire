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
        Schema::create('promo_barang', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('id_promo')->constrained('promo')->onDelete('cascade');
            $table->foreignUlid('id_barang')->constrained('barang')->onDelete('cascade');
            $table->unsignedInteger('syarat_qty')->default(1);
            $table->enum('tipe_qty', ['MINIMAL', 'KELIPATAN'])->default('MINIMAL');
            $table->foreignUlid('id_satuan')->constrained('satuan');
            $table->foreignUlid('id_barang_bonus')->nullable()->constrained('barang');
            $table->enum('tipe', ['BARANG BONUS', 'PERSEN', 'HARGA'])->default('BARANG BONUS');
            $table->unsignedInteger('bonus_qty')->nullable()->default(1);
            $table->foreignUlid('id_satuan_bonus')->nullable()->constrained('satuan');
            $table->decimal('diskon_persen', 5, 2)->nullable();
            $table->decimal('diskon_nominal', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_barang');
    }
};
