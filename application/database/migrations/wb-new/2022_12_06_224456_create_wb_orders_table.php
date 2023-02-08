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
        Schema::create('wb_orders', function (Blueprint $table) {
            $table->id();
//            $table->unsignedBigInteger('account_id');
            $table->string('g_number', 50);
            $table->date('date');
            $table->dateTime('last_change_date');
            $table->string('supplier_article', 75);
            $table->string('tech_size', 30);
            $table->string('barcode', 30);
            $table->float('total_price');
            $table->integer('discount_percent');
            $table->string('warehouse_name', 50);
            $table->string('oblast', 200);
            $table->unsignedBigInteger('income_id');
            $table->unsignedBigInteger('odid');
            $table->unsignedBigInteger('nm_id');
            $table->string('subject', 50);
            $table->string('category', 50);
            $table->string('brand', 50);
            $table->boolean('is_cancel');
            $table->dateTime('cancel_dt');
            $table->string('sticker');
            $table->string('srid');
            $table->timestamps();

            $table->unique(['date', 'last_change_date', 'barcode', 'odid', 'g_number', 'is_cancel'], 'wb_orders_unique');

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
        Schema::dropIfExists('wb_orders');
    }
};
