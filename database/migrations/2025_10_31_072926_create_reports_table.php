<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            // PK de reports (puede ser unsigned; no afecta a FKs)
            $table->increments('report_id');

            // FKs a users: users.user_id = INT (firmado)
            $table->integer('user_id')->index(); // quien reporta
            $table->foreign('user_id')
                  ->references('user_id')->on('users')
                  ->cascadeOnDelete();

            $table->integer('reported_user_id')->nullable()->index(); // usuario reportado (opcional)
            $table->foreign('reported_user_id')
                  ->references('user_id')->on('users')
                  ->nullOnDelete();

            $table->integer('resolved_by')->nullable()->index(); // admin que resuelve (opcional)
            $table->foreign('resolved_by')
                  ->references('user_id')->on('users')
                  ->nullOnDelete();

            // FK a user_products: user_products.user_product_id = UNSIGNED (increments)
            $table->unsignedInteger('user_product_id')->nullable()->index();
            $table->foreign('user_product_id')
                  ->references('user_product_id')->on('user_products')
                  ->nullOnDelete();

            // FK a products: products.products_id = INT (firmado)
            $table->integer('product_id')->nullable()->index();
            $table->foreign('product_id')
                  ->references('products_id')->on('products')
                  ->nullOnDelete();

            // Datos del reporte
            $table->string('reason', 255);
            $table->string('status', 20)->default('pending')->index(); // pending|resolved
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            // Evitar duplicados del mismo usuario contra el mismo target
            $table->unique(['user_id','product_id'], 'uniq_user_product_report');
            $table->unique(['user_id','user_product_id'], 'uniq_user_userproduct_report');
            $table->unique(['user_id','reported_user_id'], 'uniq_user_reporteduser_report');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
