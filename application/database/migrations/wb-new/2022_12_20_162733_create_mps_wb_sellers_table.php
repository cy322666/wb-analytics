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
        Schema::create('mps_wb_sellers', function (Blueprint $table) {
            $table->id('pk');
//            $table->unsignedBigInteger('account_id');
            $table->string('id')->nullable();
            $table->string('name')->nullable();
            $table->string('brand')->nullable();
            $table->string('seller')->nullable();
            $table->string('supplier_id')->nullable();
            $table->string('color')->nullable();
            $table->string('balance')->nullable();
            $table->string('balance_fbs')->nullable();
            $table->string('comments')->nullable();
            $table->string('rating')->nullable();
            $table->string('final_price')->nullable();
            $table->string('final_price_max')->nullable();
            $table->string('final_price_min')->nullable();
            $table->string('final_price_average')->nullable();
            $table->string('basic_sale')->nullable();
            $table->string('basic_price')->nullable();
            $table->string('promo_sale')->nullable();
            $table->string('client_sale')->nullable();
            $table->string('client_price')->nullable();
            $table->string('start_price')->nullable();
            $table->string('sales')->nullable();
            $table->string('sales_per_day_average')->nullable();
            $table->string('revenue')->nullable();
            $table->string('revenue_potential')->nullable();
            $table->string('lost_profit')->nullable();
            $table->string('lost_profit_percent')->nullable();
            $table->string('days_in_stock')->nullable();
            $table->string('days_with_sales')->nullable();
            $table->string('average_if_in_stock')->nullable();
            $table->string('is_fbs')->nullable();
            $table->string('subject_id')->nullable();
            $table->string('country')->nullable();
            $table->string('gender')->nullable();
            $table->string('sku_first_date')->nullable();
            $table->string('firstcommentdate')->nullable();
            $table->string('picscount')->nullable();
            $table->string('has3d')->nullable();
            $table->string('hasvideo')->nullable();
            $table->string('commentsvaluation')->nullable();
            $table->string('cardratingval')->nullable();
            $table->string('categories_last_count')->nullable();
            $table->string('category')->nullable();
            $table->string('category_position')->nullable();
            $table->json('product_visibility_graph')->nullable();
            $table->json('category_graph')->nullable();
            $table->json('graph')->nullable();
            $table->json('stocks_graph')->nullable();
            $table->json('price_graph')->nullable();
            $table->string('thumb')->nullable();
            $table->string('thumb_middle')->nullable();
            $table->string('url')->nullable();
            $table->string('subject')->nullable();
            $table->string('purchase')->nullable();
            $table->string('purchase_after_return')->nullable();
            $table->date('date');

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
        Schema::dropIfExists('mps_wb_sellers');
    }
};
