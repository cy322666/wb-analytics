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
        Schema::table('wb_stocks', function (Blueprint $table) {
            $table->dateTime('last_change_date', 3)->nullable()->change();
            $table->boolean('is_supply')->nullable()->change();
            $table->boolean('is_realization')->nullable()->change();
            $table->integer('quantity_full')->nullable()->change();
            $table->integer('quantity_not_in_orders')->nullable()->change();
            $table->integer('in_way_to_client')->nullable()->change();
            $table->integer('in_way_from_client')->nullable()->change();
            $table->string('category', 50)->nullable()->change();
            $table->integer('days_on_site')->nullable()->change();
            $table->string('sc_code', 50)->nullable()->change();
            $table->float('price')->nullable()->change();
            $table->float('discount')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
