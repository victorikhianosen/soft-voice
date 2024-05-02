<?php

use App\Models\Plan;
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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('paystack_id')->nullable();
            $table->string('slug');
            $table->integer('duration');
            $table->double('amount_ngn');
            $table->double('amount_usd');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        $plans = [
            [
                'name'     => 'Basic',
                'slug'     => 'basic',
                'duration' => 1,
                'amount_ngn'   => 0,
                'amount_usd'   => 0,
            ],
            [
                'name'     => 'Professional Monthly',
                'slug'     => 'professional-monthly',
                'paystack_id' => 'PLN_4htnmxqlj0u0svk',
                'duration' => 1,
                'amount_ngn'   => 1000,
                'amount_usd'   => 2.9,
            ],
            [
                'name'     => 'Professional Annual',
                'slug'     => 'professional-annual',
                'paystack_id' => 'PLN_4htnmxqlj0u0svk',
                'duration' => 12,
                'amount_ngn'   => 10000,
                'amount_usd'   => 29,
            ],
            [
                'name'     => 'Premium Monthly',
                'slug'     => 'premium-monthly',
                'paystack_id' => 'PLN_6bzmno4rb15wzue',
                'duration' => 1,
                'amount_ngn'   => 2000,
                'amount_usd'   => 4.9,
            ],
            [
                'name'     => 'Premium Annual',
                'slug'     => 'premium-annual',
                'paystack_id' => 'PLN_6bzmno4rb15wzue',
                'duration' => 12,
                'amount_ngn'   => 20000,
                'amount_usd'   => 49,
            ]
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
};
