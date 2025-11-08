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
        Schema::create('products', function (Blueprint $table) {
            $table->integer('products_id', true);
            $table->string('name', 100);
            $table->integer('product_type_id')->index('products_ibfk_1');
            $table->text('description');
            $table->decimal('price', 10);
            $table->integer('stock');
            $table->string('img_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
