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
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('estimate_id')->unique();
            $table->foreignId('customer_id')->constrained();
            $table->date('date');
            $table->date('due_date');
            $table->string('currency');
            $table->boolean('tax_enabled');
            $table->text('notes')->nullable();
            $table->text('additional_charges')->default('[]');
            $table->text('more_discount')->default('[]');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estimates');
    }
};
