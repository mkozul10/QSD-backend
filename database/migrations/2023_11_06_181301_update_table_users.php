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
        Schema::table('products', function(Blueprint $table) {

           
            $table->float('total_rating',8,2)->default(0);
            $table->float('avg_rating',8,2)->default(0);
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function(Blueprint $table) {

            $table->dropColumn('total_rating');
            $table->dropColumn('avg_rating');

        });
    }
};
