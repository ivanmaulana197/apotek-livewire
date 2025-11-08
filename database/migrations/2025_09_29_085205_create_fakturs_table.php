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
        Schema::create('faktur', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('no_faktur')->unique();
            $table->foreignUlid('id_supplier')->nullable()->constrained('supplier')->nullOnDelete();
            $table->integer('total_biaya');
            $table->date('tgl_jatuh_tempo');
            $table->enum('status', ['LUNAS', 'BELUM LUNAS']);
            $table->foreignUlid('id_user')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faktur');
    }
};
