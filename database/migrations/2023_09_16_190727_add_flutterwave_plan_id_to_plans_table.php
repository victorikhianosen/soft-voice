<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('stripe_id')->after('paystack_id')->nullable();
            $table->string('paypal_id')->after('stripe_id')->nullable();
            $table->string('flutterwave_id')->after('paypal_id')->nullable();
            $table->double('amount_ngn')->after('duration')->default(0);
            $table->double('amount_usd')->after('amount_ngn')->default(0);
            $table->dropColumn('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['stripe_id', 'paypal_id', 'flutterwave_id', 'amount_ngn', 'amount_usd']);
            $table->double('amount')->after('duration')->default(0);
        });
    }
};
