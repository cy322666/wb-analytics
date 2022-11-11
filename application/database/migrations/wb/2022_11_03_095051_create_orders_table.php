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
        Schema::create('wb_supplier_orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->dateTime('date')->nullable();
            $table->dateTime('last_change_date')->nullable();
            $table->string('supplier_article')->nullable();
            $table->integer('tech_size')->nullable();
            $table->bigInteger('barcode')->nullable();
            $table->float('total_price')->nullable();
            $table->integer('discount_percent')->nullable();
            $table->string('warehouse_name')->nullable();
            $table->string('oblast')->nullable();
            $table->integer('income_id')->nullable();
            $table->bigInteger('od_id')->nullable();
            $table->integer('nm_id')->nullable();
            $table->string('subject')->nullable();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->boolean('is_cancel')->nullable();
            $table->string('cancel_dt')->nullable();
            $table->string('g_number')->nullable();
            $table->string('sticker')->nullable();
            $table->string('sr_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wb_supplier_orders');
    }
};
