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
        Schema::create('pesanan_kasir', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('no_pesanan_kasir')->unique();
            $table->foreignUlid('id_member')->nullable()->constrained('member')->onDelete('cascade');
            $table->string('mode_kasir');
            $table->string('mode_shift');
            $table->integer('total_harga');
            $table->foreignUlid("id_metode_pembayaran")->constrained('metode_pembayaran');
            $table->foreignUlid('id_user')->constrained('users')->onDelete('cascade');

            $table->foreignUlid('id_dokter')->nullable()->constrained('dokter');
            $table->string('nama_pasien')->nullable();
            $table->string('alamat_pasien')->nullable();
            $table->string('no_hp_pasien')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan_kasir');
    }
};
