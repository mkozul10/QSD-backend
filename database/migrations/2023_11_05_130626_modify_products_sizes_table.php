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
        Schema::table("products_sizes", function (Blueprint $table) {
            $table->foreign('products_id')->references('id')->on('products')
                ->onUpdate('cascade')->onDelete('cascade');
            
            $table->foreign('sizes_id')->references('id')->on('sizes')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("products_sizes", function (Blueprint $table) {
            $table->dropForeign(['products_id']);
            
            $table->dropForeign(['sizes_id']);
        });
    }
};
