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
        Schema::table("categories_products", function (Blueprint $table) {
            $table->foreign('products_id')->references('id')->on('products')
                ->onUpdate('cascade')->onDelete('cascade');
            
            $table->foreign('categories_id')->references('id')->on('categories')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("categories_products", function (Blueprint $table) {
            $table->dropForeign(['products_id']);
            $table->dropForeign(['categories_id']);
        });
    }
};
