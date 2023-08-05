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
            $table->string('code')->unique();
            $table->foreignId('user_id')->constrained();
            $table->string('estimate_id');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->date('due_date');
            $table->string('currency');
            $table->boolean('tax_enabled');
            $table->text('notes')->nullable();
            $table->text('description');
            $table->text('additional_charges')->nullable();
            $table->text('more_discount')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->unique(['estimate_id', 'user_id']);
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
