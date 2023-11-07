<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */


     //missing attributes: transaction_id, total_price, user_id, state, comment, and guest_email

    public function up(): void
    {
        Schema::table('orders', function(Blueprint $table) {

           // $table->foreign('transaction_id')->references('id')->on('orders') //odakle uzimamo transaction id?
           //->onUpdate('cascade')->onDelete('cascade');


            

            $table->foreignId('users_id')->references('id')->on('users')
            ->onUpdate('cascade')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function(Blueprint $table) {

           // $table->dropForeign(['transaction_id']);
           $table->dropColumn('transaction_id');
            $table->dropColumn('total_price');
            $table->dropForeign(['user_id']);
            $table->dropColumn('state');
            $table->dropColumn('comment');
            $table->dropColumn('guest_email');

        });
    }
};
