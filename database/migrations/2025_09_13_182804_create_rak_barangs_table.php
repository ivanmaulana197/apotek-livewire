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
        Schema::create('rak_barang', function (Blueprint $table) {
            $table->foreignUlid('id_rak')->constrained('rak')->onDelete('cascade');
            $table->foreignUlid('id_barang')->constrained('barang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rak_barang');
    }
};
