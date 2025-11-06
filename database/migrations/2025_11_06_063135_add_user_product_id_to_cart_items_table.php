<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Columna para piezas de usuario (nullable para no romper productos oficiales)
            $table->unsignedBigInteger('user_product_id')
                  ->nullable()
                  ->after('products_id');

            // Opcional: si quieres relación fuerte con user_products (solo si coincide el tipo)
            // $table->foreign('user_product_id')
            //       ->references('user_product_id')
            //       ->on('user_products')
            //       ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Si creaste foreign key, descomenta esto primero:
            // $table->dropForeign(['user_product_id']);
            $table->dropColumn('user_product_id');
        });
    }
};
