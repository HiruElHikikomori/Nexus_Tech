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
        Schema::table('user_products', function (Blueprint $table) {
            $table->foreign(['product_type_id'])->references(['product_type_id'])->on('product_types')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->dropForeign('user_products_product_type_id_foreign');
            $table->dropForeign('user_products_user_id_foreign');
        });
    }
};
