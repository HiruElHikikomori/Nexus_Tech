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
        Schema::table('reports', function (Blueprint $table) {
            $table->foreign(['product_id'])->references(['products_id'])->on('products')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['reported_user_id'])->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['resolved_by'])->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['user_id'])->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['user_product_id'])->references(['user_product_id'])->on('user_products')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign('reports_product_id_foreign');
            $table->dropForeign('reports_reported_user_id_foreign');
            $table->dropForeign('reports_resolved_by_foreign');
            $table->dropForeign('reports_user_id_foreign');
            $table->dropForeign('reports_user_product_id_foreign');
        });
    }
};
