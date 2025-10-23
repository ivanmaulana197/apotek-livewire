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
        Schema::create('setting', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('nama');
            $table->text('alamat');
            $table->string('kabupaten');
            $table->string('email')->nullable();
            $table->string('no_telp');
            $table->string('link_website')->nullable();
            $table->string('link_tiktok')->nullable();
            $table->string('link_instagram')->nullable();
            $table->string('link_facebook')->nullable();
            $table->text('link_google_map')->nullable();
            $table->text('logo')->nullable();
            $table->integer('pembulatan_resep')->nullable();
            $table->integer('pembulatan_harga_barang')->nullable();
            $table->integer('pembulatan_total')->nullable();
            $table->integer('persentase_jual')->nullable();
            $table->integer('persentase_jual_resep')->nullable();
            $table->text('footer_struk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting');
    }
};