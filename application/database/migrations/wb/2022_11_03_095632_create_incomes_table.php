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
        Schema::create('wb_supplier_incomes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('income_id')->nullable();
            $table->string('number')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('last_change_date')->nullable();
            $table->string('supplier_article')->nullable();
            $table->integer('tech_size')->nullable();
            $table->bigInteger('barcode')->nullable();
            $table->integer('quantity')->nullable();
            $table->float('total_price')->nullable();
            $table->dateTime('date_close')->nullable();
            $table->string('warehouse_name')->nullable();
            $table->integer('nm_id')->nullable();
            $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wb_supplier_incomes');
    }
};
