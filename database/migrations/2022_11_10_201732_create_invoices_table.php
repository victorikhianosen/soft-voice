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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('invoice_id');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->date('due_date');
            $table->boolean('tax_enabled');
            $table->boolean('recurring');
            $table->boolean('accept_payment')->default(false);
            $table->string('currency');
            $table->text('recurring_duration')->nullable();
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->text('additional_charges')->nullable();
            $table->text('more_discount')->nullable();
            $table->string('signature')->nullable();
            $table->string('signatory_name')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->unique(['invoice_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};