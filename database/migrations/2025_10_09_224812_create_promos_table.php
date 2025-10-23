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
        Schema::create('promo', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['BARANG', 'TRANSAKSI']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->boolean('is_member')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo');
    }
};