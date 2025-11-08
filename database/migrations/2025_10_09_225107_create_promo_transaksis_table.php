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
        Schema::create('promo_transaksi', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('id_promo')->constrained('promo')->onDelete('cascade');
            $table->decimal('minimal_belanja', 8, 0)->default(0);
            $table->enum('tipe_transaksi', ['PERSEN', 'HARGA'])->default('PERSEN');
            $table->decimal('diskon_persen', 5, 2)->nullable();
            $table->decimal('diskon_nominal', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_transaksi');
    }
};
