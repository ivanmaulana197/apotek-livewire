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
        Schema::create('history_barang', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('id_barang')->nullable()->constrained('barang')->nullOnDelete();
            $table->smallInteger('jumlah');
            $table->foreignUlid('id_satuan')->nullable()->constrained('satuan')->nullOnDelete();
            // [{'tgl_expired': '2028-04-1', 'jumlah': 3}, ...]
            $table->json('tgl_expired')->nullable();
            $table->enum('status', ['MASUK', 'TERJUAL', 'RETUR', 'EXPIRED BUANG', 'EXPIRED GANTI / RETUR', 'HILANG', 'RUSAK BUANG', 'RUSAK GANTI', 'PINDAH']);
            // $table->nullableUlidMorphs('referensi');
            $table->foreignUlid('id_user')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_barang');
    }
};
