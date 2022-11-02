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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('token')->nullable();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);

            $table->dateTime('last_updated_at')->nullable();
            $table->dateTime('expired_at')->nullable();

            $table->integer('user_id')->unsigned();

            $table->string('db_host')->nullable();
            $table->string('db_username')->nullable();
            $table->string('db_password')->nullable();
            $table->string('db_port')->nullable();
            $table->string('db_type')->nullable();
            $table->string('db_name')->nullable();
        });

        Schema::table('accounts', function (Blueprint $table) {

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
        Schema::dropIfExists('accounts');
    }
};
