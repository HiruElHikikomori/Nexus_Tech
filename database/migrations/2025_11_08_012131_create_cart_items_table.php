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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->integer('id_cart_items', true);
            $table->integer('cart_id')->index('cart_id');
            $table->unsignedBigInteger('products_id')->nullable()->index('products_id');
            $table->unsignedBigInteger('user_product_id')->nullable();
            $table->integer('count');
            $table->decimal('unit_price', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
