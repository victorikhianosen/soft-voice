<?php

use App\Models\Admin;
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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        $admins = [
            [
                'name'     => 'Softweb',
                'email'    => 'softwebdigital@gmail.com',
                'password' => '$2y$10$HSFy8n8MJtIwxEusT5L3P.ZLXlHh78VEtZvFY1LdS1/NfgTfKq9uS'
            ],
            [
                'name'     => 'Invoice Solution',
                'email'    => 'admin@softinvoice.co',
                'password' => '$2y$10$HSFy8n8MJtIwxEusT5L3P.ZLXlHh78VEtZvFY1LdS1/NfgTfKq9uS'
            ],
            [
                'name'     => 'Babatunde',
                'email'    => 'babatundehezekiah7@gmail.com',
                'password' => '$2y$10$HSFy8n8MJtIwxEusT5L3P.ZLXlHh78VEtZvFY1LdS1/NfgTfKq9uS'
            ]
        ];

        foreach ($admins as $admin) {
            Admin::create($admin);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
};
