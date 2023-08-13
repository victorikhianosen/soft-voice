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
        Schema::table('online_payments', function (Blueprint $table) {
            $table->string('type')->after('id');
            $table->string('reference')->after('type');
            $table->double('amount')->after('reference');
            $table->string('model_type')->after('amount');
            $table->integer('model_id')->after('model_type');
            $table->string('status')->default('pending')->after('model_id');
            $table->string('gateway')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('online_payments', function (Blueprint $table) {
            $table->dropColumn(['type', 'reference', 'amount', 'model_type', 'model_id', 'status', 'gateway']);
        });
    }
};
