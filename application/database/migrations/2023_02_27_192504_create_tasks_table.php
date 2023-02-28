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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->integer('user_id')->unsigned();
            $table->integer('account_id')->unsigned();

            $table->string('command')->nullable();
            $table->json('params')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('completed')->default(false);
        });

        Schema::table('tasks', function (Blueprint $table) {

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
