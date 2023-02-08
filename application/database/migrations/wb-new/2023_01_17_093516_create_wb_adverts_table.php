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
        Schema::create('wb_adverts', function (Blueprint $table) {
            $table->id();
//            $table->unsignedBigInteger('account_id');
            $table->date('date');
            $table->unsignedBigInteger('advert_id');
            $table->unsignedInteger('type')->nullable();
            $table->string('type_name')->nullable();
            $table->unsignedInteger('status')->nullable();
            $table->string('status_name')->nullable();
            $table->dateTime('create_time', 6)->nullable();
            $table->dateTime('change_time', 6)->nullable();
            $table->unsignedInteger('param_index');
            $table->json('intervals');
            $table->integer('daily_budget')->nullable();
            $table->integer('price')->nullable();
            $table->unsignedBigInteger('menu_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_name')->nullable();
            $table->unsignedBigInteger('set_id')->nullable();

//            $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wb_adverts');
    }
};
