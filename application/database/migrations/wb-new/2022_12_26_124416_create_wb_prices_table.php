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
        Schema::create('wb_prices', function (Blueprint $table) {
            $table->id();
            $table->date('date');
//            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('nm_id');
            $table->float('price')->nullable();
            $table->integer('discount')->nullable();
            $table->float('promo_code')->nullable();

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
        Schema::dropIfExists('wb_prices');
    }
};
