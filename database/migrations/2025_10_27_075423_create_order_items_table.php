<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
Schema::create('order_items', function (Blueprint $t) {
  $t->engine = 'InnoDB';
  $t->bigIncrements('order_item_id');

  $t->unsignedBigInteger('order_id');   // empata con orders.order_id (bigIncrements)
  $t->integer('product_id')->nullable();       // empata con products.products_id (int firmado)
  $t->unsignedInteger('user_product_id')->nullable();  // empata con user_products.user_product_id (int firmado)

  $t->integer('count');
  $t->decimal('unit_price', 10, 2);
  $t->timestamps();

  $t->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
  $t->foreign('product_id')->references('products_id')->on('products')->onDelete('set null');
  $t->foreign('user_product_id')->references('user_product_id')->on('user_products')->onDelete('set null');

  $t->index('product_id');
  $t->index('user_product_id');
});
  }
  public function down(): void { Schema::dropIfExists('order_items'); }
};
