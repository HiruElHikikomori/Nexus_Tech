<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('user_products', function (Blueprint $t) {
      $t->increments('user_product_id');
      $t->integer('user_id');
      $t->integer('product_type_id');
      $t->string('name', 100);
      $t->text('description');
      $t->decimal('price', 10, 2);
      $t->integer('stock');
      $t->string('img_name', 255)->default('default.png');
      $t->string('condition', 50); // Nuevo/Usado
      $t->integer('report_count')->default(0);
      $t->timestamps();

      $t->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
      $t->foreign('product_type_id')->references('product_type_id')->on('product_types')->onDelete('restrict');
      $t->index(['product_type_id','price']);
    });
  }
  public function down(): void { Schema::dropIfExists('user_products'); }
};
