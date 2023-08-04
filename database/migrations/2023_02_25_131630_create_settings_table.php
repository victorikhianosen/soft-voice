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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('currency')->default('NGN');
            $table->mediumText('business_logo')->nullable();
            $table->boolean('auto_payment_reminder')->default(false);
            $table->string('invoice_prefix')->default('INV-');
            $table->string('estimate_prefix')->default('EST-');
            $table->string('service_category')->nullable();
            $table->mediumText('signature')->nullable();
            $table->string('signatory_name')->nullable();
            $table->dateTime('preferred_due_date')->nullable();
            $table->boolean('payment_history_on_invoice_page')->default(false);
            $table->boolean('auto_enable_tax')->default(false);
            $table->boolean('auto_enable_bank_account')->default(false);
            $table->foreignId('preferred_bank_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->double('tax_percentage')->default(0);
            $table->string('theme')->default('light');
            $table->string('timezone')->nullable();
            $table->string('date_format')->nullable();
            $table->string('financial_year')->nullable();
            $table->text('notifications')->nullable();
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
        Schema::dropIfExists('settings');
    }
};
