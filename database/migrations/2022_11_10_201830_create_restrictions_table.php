<?php

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
        Schema::create('restrictions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('unit', ['count']);
            $table->timestamps();
        });

        $restrictions = [
            [
                'name' => 'invoices',
                'unit' => 'count',
            ],
            [
                'name' => 'expenses',
                'unit' => 'count',
            ],
            [
                'name' => 'estimates',
                'unit' => 'count',
            ],
            [
                'name' => 'customers',
                'unit' => 'count',
            ],
        ];

        foreach ($restrictions as $restriction) {
            Restriction::create($restriction);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restrictions');
    }
};
