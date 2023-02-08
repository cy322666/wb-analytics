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
        Schema::create('wb_adverts_cpm', function (Blueprint $table) {
            $table->id();
//            $table->unsignedBigInteger('account_id');
            $table->date('date');
            $table->unsignedInteger('type')->nullable();
            $table->string('type_name')->nullable();
            $table->unsignedBigInteger('param')->nullable();
            $table->integer('cmp')->nullable();
            $table->integer('count')->nullable();

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
        Schema::dropIfExists('wb_adverts_cpm');
    }
};
