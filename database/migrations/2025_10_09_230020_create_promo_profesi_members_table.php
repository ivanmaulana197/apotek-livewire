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
        Schema::create('promo_profesi_member', function (Blueprint $table) {
            $table->foreignUlid('id_promo')->constrained('promo')->onDelete('cascade');
            $table->foreignUlid('id_profesi')->constrained('profesi')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_profesi_member');
    }
};
