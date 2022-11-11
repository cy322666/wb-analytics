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
        Schema::create('wb_supplier_sales', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->dateTime('date')->nullable();
            $table->dateTime('last_change_date')->nullable();
            $table->string('supplier_article')->nullable();
            $table->string('tech_size')->nullable();
            $table->string('barcode')->nullable();
            $table->float('total_price')->nullable();
            $table->float('discount_percent')->nullable();
            $table->boolean('is_supply')->nullable();
            $table->boolean('is_realization')->nullable();
            $table->float('promo_code_discount')->nullable();
            $table->string('warehouse_name')->nullable();
            $table->string('country_name')->nullable();
            $table->string('oblast_okrug_name')->nullable();
            $table->string('region_name')->nullable();
            $table->integer('income_id')->nullable();
            $table->string('sale_id')->nullable();
            $table->string('od_id')->nullable();
            $table->float('spp')->nullable();
            $table->float('for_pay')->nullable();
            $table->float('finished_price')->nullable();
            $table->float('price_with_disc')->nullable();
            $table->integer('nm_id')->nullable();
            $table->string('subject')->nullable();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->float('is_storno')->nullable();
            $table->string('g_number')->nullable();
            $table->string('sticker')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wb_supplier_sales');
    }
};
