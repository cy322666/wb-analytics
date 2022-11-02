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
            $table->timestamps();

            $table->string("order_id")->nullable();
            $table->string("date_created")->nullable();
            $table->string("wb_wh_id")->nullable();
            $table->string("store_id")->nullable();
            $table->string("pid")->nullable();
            $table->string("office_address")->nullable();
            $table->string("office_latitude")->nullable();
            $table->string("office_longitude")->nullable();
            $table->string("delivery_address")->nullable();
            $table->string("province")->nullable();
            $table->string("area")->nullable();
            $table->string("city")->nullable();
            $table->string("street")->nullable();
            $table->string("home")->nullable();
            $table->string("flat")->nullable();
            $table->string("entrance")->nullable();
            $table->string("longitude")->nullable();
            $table->string("latitude")->nullable();
            $table->string("user_id")->nullable();
            $table->string("fio")->nullable();
            $table->string("phone")->nullable();
            $table->string("chrt_id")->nullable();
            $table->string("barcode")->nullable();
            $table->string("barcodes")->nullable();
            $table->string("sc_offices_names")->nullable();
            $table->string("status")->nullable();
            $table->string("user_status")->nullable();
            $table->string("rid")->nullable();
            $table->string("total_price")->nullable();
            $table->string("order_uid")->nullable();
            $table->string("delivery_type")->nullable();

            $table->string("number")->nullable();
            $table->string("date")->nullable();
            $table->string("last_change_date")->nullable();
            $table->string("supplier_article")->nullable();
            $table->string("techSize")->nullable();
            $table->string("quantity")->nullable();
            $table->string("discount_percent")->nullable();
            $table->string("warehouse_name")->nullable();
            $table->string("oblast")->nullable();
            $table->string("income_id")->nullable();
            $table->string("odid")->nullable();
            $table->string("nm_id")->nullable();
            $table->string("subject")->nullable();
            $table->string("category")->nullable();
            $table->string("brand")->nullable();
            $table->string("is_cancel")->nullable();
            $table->string("cancel_dt")->nullable();
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
        Schema::dropIfExists('wb_orders');
    }
};
