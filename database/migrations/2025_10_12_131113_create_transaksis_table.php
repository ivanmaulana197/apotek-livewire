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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('no_transaksi')->unique();
            $table->bigInteger('sub_total');
            $table->bigInteger('potongan_diskon');
            $table->bigInteger('total_bayar');
            $table->integer('pembulatan')->default(0);
            $table->bigInteger('bayar');
            $table->integer('kembalian');

            $table->foreignUlid('id_user')->constrained('users');
            $table->foreignUlid('id_metode_pembayaran')->constrained('metode_pembayaran');
            $table->enum('jenis_transaksi',['UMUM','MEMBERSHIP','RESEP','MEMBERSHIP RESEP']);
            $table->enum('shift_transaksi',['PAGI','MALAM']);
            $table->enum('status',['TERJUAL','RETUR','RETUR SEBAGIAN']);




            $table->foreignUlid('id_member')->nullable()->constrained('member');
            $table->foreignUlid('id_pasien')->nullable()->constrained('pasien');
            $table->foreignUlid('id_dokter')->nullable()->constrained('dokter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
