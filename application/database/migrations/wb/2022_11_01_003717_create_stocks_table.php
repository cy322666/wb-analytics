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
        Schema::create('wb_stocks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string("last_change_date")->nullable();
            $table->string("supplier_article")->nullable();
            $table->string("tech_size")->nullable();
            $table->string("barcode")->nullable();
            $table->string("quantity")->nullable();
            $table->string("is_supply")->nullable();
            $table->string("is_realization")->nullable();
            $table->string("quantity_full")->nullable();
            $table->string("quantity_not_in_orders")->nullable();
            $table->string("warehouse_name")->nullable();
            $table->string("in_way_to_client")->nullable();
            $table->string("in_way_from_client")->nullable();
            $table->string("nm_id")->nullable();
            $table->string("subject")->nullable();
            $table->string("category")->nullable();
            $table->string("days_on_site")->nullable();
            $table->string("brand")->nullable();
            $table->string("scc_ode")->nullable();
            $table->string("price")->nullable();
            $table->string("discount")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wb_stocks');
    }
};
