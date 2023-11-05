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
        Schema::table("orders_products_sizes", function (Blueprint $table) {
            $table->foreign('orders_id')->references('id')->on('orders')
                ->onUpdate('cascade')->onDelete('cascade');
            
            $table->foreign('products_sizes_id')->references('id')->on('products_sizes')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("orders_products_sizes", function (Blueprint $table) {
            $table->dropForeign('orders_id');
            $table->dropForeign('products_sizes_id');
        });
    }
};
