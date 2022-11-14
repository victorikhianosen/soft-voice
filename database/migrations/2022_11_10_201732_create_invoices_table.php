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
            $table->string('invoice_id')->unique();
            $table->string('po_number')->nullable()->unique();
            $table->foreignId('customer_id')->constrained();
            $table->date('date');
            $table->date('due_date');
            $table->boolean('tax_enabled');
            $table->boolean('recurring');
            $table->boolean('round_off');
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->json('additional_charges')->default('[]');
            $table->json('more_discount')->default('[]');
            $table->string('signature')->nullable();
            $table->string('signatory_name')->nullable();
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
        Schema::dropIfExists('invoices');
    }
};
