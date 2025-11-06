<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // 👇 ¡QUITAMOS ESTA LÍNEA QUE ROMPE!
            // $table->dropForeign('cart_items_ibfk_2');

            // Solo intentamos hacer products_id nullable
            $table->unsignedBigInteger('products_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Volvemos a NOT NULL si hacemos rollback
            $table->unsignedBigInteger('products_id')->nullable(false)->change();
        });
    }
};
