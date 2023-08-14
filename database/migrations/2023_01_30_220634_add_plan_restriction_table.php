<?php

use App\Models\Plan;
use App\Models\Restriction;
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
        Schema::create('plan_restriction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');;
            $table->foreignId('restriction_id')->constrained()->onDelete('cascade');;
            $table->integer('value');
            $table->timestamps();
        });

        $basic = Plan::where('slug', 'basic')->first();
        $basic->restrictions()->attach(1, ['value' => 5]);
        $basic->restrictions()->attach(2, ['value' => 10]);
        $basic->restrictions()->attach(3, ['value' => 20]);
        $basic->restrictions()->attach(4, ['value' => -1]);
        $basic->restrictions()->attach(5, ['value' => 1]);
        $basic->restrictions()->attach(6, ['value' => -1]);
        $basic->restrictions()->attach(7, ['value' => 1]);
        $basic->restrictions()->attach(8, ['value' => 1]);
        $basic->restrictions()->attach(9, ['value' => 1]);
        $basic->restrictions()->attach(10, ['value' => 0]);
        $basic->restrictions()->attach(11, ['value' => 0]);
        $basic->restrictions()->attach(12, ['value' => 0]);
        $basic->restrictions()->attach(13, ['value' => 0]);
        $basic->restrictions()->attach(14, ['value' => 0]);

        Plan::where('slug', 'LIKE', 'professional%')->get()->each(function ($plan) {
            $plan->restrictions()->attach(1, ['value' => -1]);
            $plan->restrictions()->attach(2, ['value' => -1]);
            $plan->restrictions()->attach(3, ['value' => -1]);
            $plan->restrictions()->attach(4, ['value' => -1]);
            $plan->restrictions()->attach(5, ['value' => 1]);
            $plan->restrictions()->attach(6, ['value' => -1]);
            $plan->restrictions()->attach(7, ['value' => 1]);
            $plan->restrictions()->attach(8, ['value' => 1]);
            $plan->restrictions()->attach(9, ['value' => 1]);
            $plan->restrictions()->attach(10, ['value' => 1]);
            $plan->restrictions()->attach(11, ['value' => 0]);
            $plan->restrictions()->attach(12, ['value' => 0]);
            $plan->restrictions()->attach(13, ['value' => 0]);
            $plan->restrictions()->attach(14, ['value' => 0]);
        });

        Plan::where('slug', 'LIKE', 'premium%')->get()->each(function ($plan) {
            $plan->restrictions()->attach(1, ['value' => -1]);
            $plan->restrictions()->attach(2, ['value' => -1]);
            $plan->restrictions()->attach(3, ['value' => -1]);
            $plan->restrictions()->attach(4, ['value' => -1]);
            $plan->restrictions()->attach(5, ['value' => 1]);
            $plan->restrictions()->attach(6, ['value' => -1]);
            $plan->restrictions()->attach(7, ['value' => 1]);
            $plan->restrictions()->attach(8, ['value' => 1]);
            $plan->restrictions()->attach(9, ['value' => 1]);
            $plan->restrictions()->attach(10, ['value' => 1]);
            $plan->restrictions()->attach(11, ['value' => 1]);
            $plan->restrictions()->attach(12, ['value' => 1]);
            $plan->restrictions()->attach(13, ['value' => 1]);
            $plan->restrictions()->attach(14, ['value' => 1]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_restriction');
    }
};
