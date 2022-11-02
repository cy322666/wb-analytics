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
        Schema::create('wb_supplies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string("number")->nullable();
            $table->string("date")->nullable();
            $table->string("last_change_date")->nullable();
            $table->string("supplier_article")->nullable();
            $table->string("tech_size")->nullable();
            $table->string("barcode")->nullable();
            $table->string("quantity")->nullable();
            $table->string("total_price")->nullable();
            $table->string("discount_percent")->nullable();
            $table->string("is_supply")->nullable();
            $table->string("is_realization")->nullable();
            $table->string("order_id")->nullable();
            $table->string("promo_code_discount")->nullable();
            $table->string("warehouse_name")->nullable();
            $table->string("country_name")->nullable();
            $table->string("oblast_okrug_name")->nullable();
            $table->string("region_name")->nullable();
            $table->string("income_id")->nullable();
            $table->string("sale_id")->nullable();
            $table->string("odid")->nullable();
            $table->string("spp")->nullable();
            $table->string("for_pay")->nullable();
            $table->string("finished_price")->nullable();
            $table->string("price_with_disc")->nullable();
            $table->string("nm_id")->nullable();
            $table->string("subject")->nullable();
            $table->string("category")->nullable();
            $table->string("brand")->nullable();
            $table->string("is_storno")->nullable();
            $table->string("g_number")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wb_supplies');
    }
};
