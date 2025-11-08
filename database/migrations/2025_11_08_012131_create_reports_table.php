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
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('report_id');
            $table->integer('user_id')->index();
            $table->integer('reported_user_id')->nullable()->index();
            $table->integer('resolved_by')->nullable()->index();
            $table->unsignedInteger('user_product_id')->nullable()->index();
            $table->integer('product_id')->nullable()->index();
            $table->string('reason');
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'product_id'], 'uniq_user_product_report');
            $table->unique(['user_id', 'reported_user_id'], 'uniq_user_reporteduser_report');
            $table->unique(['user_id', 'user_product_id'], 'uniq_user_userproduct_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
