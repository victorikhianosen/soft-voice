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
            $table->double('amount');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        $plans = [
            [
                'name'     => 'Basic',
                'slug'     => 'basic',
                'duration' => 1,
                'amount'   => 0,
            ],
            [
                'name'     => 'Professional Monthly',
                'slug'     => 'professional-monthly',
                'paystack_id' => 'PLN_4htnmxqlj0u0svk',
                'duration' => 1,
                'amount'   => 1000,
            ],
            [
                'name'     => 'Professional Annual',
                'slug'     => 'professional-annual',
                'paystack_id' => 'PLN_4htnmxqlj0u0svk',
                'duration' => 12,
                'amount'   => 10000,
            ],
            [
                'name'     => 'Premium Monthly',
                'slug'     => 'premium-monthly',
                'paystack_id' => 'PLN_6bzmno4rb15wzue',
                'duration' => 1,
                'amount'   => 2000,
            ],
            [
                'name'     => 'Premium Annual',
                'slug'     => 'premium-annual',
                'paystack_id' => 'PLN_6bzmno4rb15wzue',
                'duration' => 12,
                'amount'   => 20000,
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
