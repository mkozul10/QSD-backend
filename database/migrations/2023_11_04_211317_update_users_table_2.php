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
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn("role");
            $table->string("address");
            $table->string("city");
            $table->string("zip_code");
            $table->string("phone");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->string("role");
            $table->dropColumn("address");
            $table->dropColumn("city");
            $table->dropColumn("zip_code");
            $table->dropColumn("phone");
        });
    }
};
