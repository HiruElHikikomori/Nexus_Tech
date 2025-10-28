<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('orders', function (Blueprint $t) {
      $t->engine = 'InnoDB';                 // fuerza InnoDB por si acaso
      $t->bigIncrements('order_id');         // BIGINT UNSIGNED (ok)
      $t->integer('user_id');                // INT firmado para empatar users.user_id
      $t->decimal('total', 10, 2);
      $t->string('status', 20)->default('paid');
      $t->timestamps();

      $t->index('user_id');                  // no es obligatorio, pero ayuda en MariaDB
      $t->foreign('user_id')
        ->references('user_id')->on('users')
        ->onDelete('cascade');
    });
  }

  public function down(): void {
    Schema::dropIfExists('orders');
  }
};
