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
        Schema::table('accounts', function (Blueprint $table) {

            $table->string('time_load')->nullable();//когда выгружать
        });

        Schema::dropIfExists('exports');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {

            $table->dropColumn('time_load');
        });

        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('user_id');
            $table->integer('account_id');
            $table->string('type');
            $table->dateTime('start_at');
            $table->dateTime('finish_at')->nullable();
            $table->integer('status')->default(0);

            $table->json('options')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('account_id')->references('id')->on('accounts');

            $table->index('type');
            $table->index('start_at');
        });
    }
};
