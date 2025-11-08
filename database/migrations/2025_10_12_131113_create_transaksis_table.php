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
            $table->integer('sub_total');
            $table->mediumInteger('potongan_diskon');
            $table->integer('total_bayar');
            $table->smallInteger('pembulatan')->default(0);
            $table->integer('bayar');
            $table->mediumInteger('kembalian');

            $table->enum('jenis_transaksi', ['UMUM', 'MEMBERSHIP', 'RESEP', 'MEMBERSHIP RESEP']);
            $table->enum('shift_transaksi', ['PAGI', 'MALAM']);
            $table->enum('status', ['TERJUAL', 'RETUR', 'RETUR SEBAGIAN']);
            $table->char('id_pasien', 26)->nullable();
            $table->foreignUlid('id_member')->nullable()->constrained('member');
            // $table->foreignUlid('id_pasien')->nullable()->constrained('pasien');
            $table->foreignUlid('id_dokter')->nullable()->constrained('dokter');
            $table->foreignUlid('id_metode_pembayaran')->constrained('metode_pembayaran');
            $table->foreignUlid('id_user')->constrained('users');
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
