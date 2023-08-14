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
            $table->enum('unit', ['count', 'boolean']);
            $table->timestamps();
        });

        $restrictions = [
            [
                'name' => 'invoices',
                'unit' => 'count',
            ],
            [
                'name' => 'estimates',
                'unit' => 'count',
            ],
            [
                'name' => 'expenses',
                'unit' => 'count',
            ],
            [
                'name' => 'customers',
                'unit' => 'count',
            ],
            [
                'name' => 'wallet',
                'unit' => 'boolean',
            ],
            [
                'name' => 'transactions',
                'unit' => 'count',
            ],
            [
                'name' => 'in-app-payments',
                'unit' => 'boolean',
            ],
            [
                'name' => 'data-export',
                'unit' => 'boolean',
            ],
            [
                'name' => 'report',
                'unit' => 'boolean',
            ],
            [
                'name' => 'custom-payment',
                'unit' => 'boolean',
            ],
            [
                'name' => 'sms-messaging',
                'unit' => 'boolean',
            ],
            [
                'name' => 'whatsapp-messaging',
                'unit' => 'boolean',
            ],
            [
                'name' => 'custom-signature',
                'unit' => 'boolean',
            ],
            [
                'name' => 'api-integration',
                'unit' => 'boolean',
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
