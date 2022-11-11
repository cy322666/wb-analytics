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
        Schema::create('wb_supplier_stocks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->dateTime('last_change_date')->nullable();
            $table->string('supplier_article')->nullable();
            $table->integer('tech_size')->nullable();
            $table->string('barcode')->nullable();
            $table->float('quantity')->nullable();
            $table->boolean('is_supply')->nullable();
            $table->boolean('is_realization')->nullable();
            $table->integer('quantity_full')->nullable();
            $table->integer('quantity_not_in_orders')->nullable();
            $table->integer('warehouse')->nullable();
            $table->string('warehouse_name')->nullable();
            $table->integer('in_way_to_client')->nullable();
            $table->integer('in_way_from_client')->nullable();
            $table->integer('nm_id')->nullable();
            $table->string('subject')->nullable();
            $table->string('category')->nullable();
            $table->integer('days_on_site')->nullable();
            $table->string('brand')->nullable();
            $table->string('scc_ode')->nullable();
            $table->float('price')->nullable();
            $table->integer('discount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wb_supplier_stocks');
    }
};
