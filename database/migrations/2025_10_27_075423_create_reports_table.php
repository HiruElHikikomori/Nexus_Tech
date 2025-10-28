<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('reports', function (Blueprint $t) {
      $t->engine = 'InnoDB';
      $t->increments('report_id');

      $t->integer('user_id');                    // INT firmado → users.user_id
      $t->integer('product_id')->nullable();     // INT firmado → products.products_id
      $t->unsignedInteger('user_product_id')->nullable(); // INT UNSIGNED → user_products.user_product_id

      $t->text('reason');
      $t->timestamps();

      $t->index('user_id');
      $t->index('product_id');
      $t->index('user_product_id');

      $t->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
      $t->foreign('product_id')->references('products_id')->on('products')->onDelete('cascade');
      $t->foreign('user_product_id')->references('user_product_id')->on('user_products')->onDelete('cascade');
    });
  }

  public function down(): void {
    Schema::dropIfExists('reports');
  }
};
