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
        Schema::create('stok_opname_histori', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('id_barang')->nullable()->constrained('barang')->onDelete('set null');
            $table->integer('stok');
            $table->integer('stok_real');
            $table->integer('kerugian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_opname_histori');
    }
};
