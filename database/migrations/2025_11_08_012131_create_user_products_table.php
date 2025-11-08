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
        Schema::create('user_products', function (Blueprint $table) {
            $table->increments('user_product_id');
            $table->integer('user_id')->index('user_products_user_id_foreign');
            $table->integer('product_type_id');
            $table->string('name', 100);
            $table->text('description');
            $table->decimal('price', 10);
            $table->integer('stock');
            $table->string('img_name')->default('default.png');
            $table->string('condition', 50);
            $table->integer('report_count')->default(0);
            $table->timestamps();

            $table->index(['product_type_id', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_products');
    }
};
