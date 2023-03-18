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

        Admin::create([
            'name'     => 'Invoice Solution',
            'email'    => 'admin@gmail.com',
            'password' => '$2y$10$HSFy8n8MJtIwxEusT5L3P.ZLXlHh78VEtZvFY1LdS1/NfgTfKq9uS'
        ]);
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
