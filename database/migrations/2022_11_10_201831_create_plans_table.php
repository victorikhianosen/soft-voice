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
                'duration' => -1,
                'amount'   => 0,
            ],
            [
                'name'     => 'Premium',
                'slug'     => 'premium',
                'duration' => 1,
                'amount'   => 5000,
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
