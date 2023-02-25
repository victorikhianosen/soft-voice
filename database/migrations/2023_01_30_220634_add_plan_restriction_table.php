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
        $basic->restrictions()->attach(1, ['value' => 10]);
        $basic->restrictions()->attach(2, ['value' => 10]);
        $basic->restrictions()->attach(3, ['value' => 10]);
        $basic->restrictions()->attach(4, ['value' => 50]);

        $premium = Plan::where('slug', 'premium')->first();
        $premium->restrictions()->attach(1, ['value' => -1]);
        $premium->restrictions()->attach(2, ['value' => -1]);
        $premium->restrictions()->attach(3, ['value' => -1]);
        $premium->restrictions()->attach(4, ['value' => -1]);
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
