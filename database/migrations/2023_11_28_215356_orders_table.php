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
        Schema::table('orders', function(Blueprint $table) {
            //$table->bigInteger('users_id')->nullable();
            $table->string('guest_email')->nullable()->default(null)->change();
            $table->string('comment')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('users_id')->change();
            $table->string('guest_email')->change();
            $table->string('comment')->change();
        });   
    }
};
