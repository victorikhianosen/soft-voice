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
        Schema::table('paypal_keys', function (Blueprint $table) {
            $table->dropColumn(['username', 'password', 'signature']);
            $table->mediumText('client_id')->nullable();
            $table->mediumText('client_secret')->nullable();
            $table->mediumText('webhook_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paypal_keys', function (Blueprint $table) {
            $table->dropColumn(['client_id', 'client_secret', 'webhook_id']);
            $table->mediumText('username')->nullable();
            $table->mediumText('password')->nullable();
            $table->mediumText('signature')->nullable();
        });
    }
};
