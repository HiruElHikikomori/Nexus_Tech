<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('products', function (Blueprint $t) {
      $t->decimal('price', 10, 2)->change();
    });
    Schema::table('cart_items', function (Blueprint $t) {
      $t->decimal('unit_price', 10, 2)->change();
    });
  }
  public function down(): void {
    Schema::table('products', function (Blueprint $t) {
      $t->decimal('price', 5, 2)->change();
    });
    Schema::table('cart_items', function (Blueprint $t) {
      $t->decimal('unit_price', 5, 2)->change();
    });
  }
};
