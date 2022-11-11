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
        Schema::create('wb_supplier_report_period', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('realizationreport_id')->nullable();
            $table->string('suppliercontract_code')->nullable();
            $table->bigInteger('rrd_id')->nullable();
            $table->bigInteger('gi_id')->nullable();
            $table->string('subject_name')->nullable();
            $table->bigInteger('nm_id')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('sa_name')->nullable();
            $table->float('ts_name')->nullable();
            $table->string('barcode')->nullable();
            $table->string('doc_type_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->float('retail_price')->nullable();
            $table->float('retail_amount')->nullable();
            $table->float('sale_percent')->nullable();
            $table->float('commission_percent')->nullable();
            $table->string('office_name')->nullable();
            $table->string('supplier_oper_name')->nullable();
            $table->dateTime('order_dt')->nullable();
            $table->dateTime('sale_dt')->nullable();
            $table->dateTime('rr_dt')->nullable();
            $table->integer('shk_id')->nullable();
            $table->float('retail_price_withdisc_rub')->nullable();
            $table->integer('delivery_amount')->nullable();
            $table->integer('return_amount')->nullable();
            $table->float('delivery_rub')->nullable();
            $table->string('gi_box_type_name')->nullable();
            $table->float('product_discount_for_report')->nullable();
            $table->float('supplier_promo')->nullable();
            $table->bigInteger('rid')->nullable();
            $table->integer('ppvz_spp_prc')->nullable();
            $table->integer('ppvz_kvw_prc_base')->nullable();
            $table->integer('ppvz_kvw_prc')->nullable();
            $table->float('ppvz_sales_commission')->nullable();
            $table->integer('ppvz_for_pay')->nullable();
            $table->integer('ppvz_reward')->nullable();
            $table->integer('ppvz_vw')->nullable();
            $table->integer('ppvz_vw_nds')->nullable();
            $table->integer('ppvz_office_id')->nullable();
            $table->string('ppvz_office_name')->nullable();
            $table->integer('ppvz_supplier_id')->nullable();
            $table->string('ppvz_supplier_name')->nullable();
            $table->integer('ppvz_inn')->nullable();
            $table->integer('declaration_number')->nullable();
            $table->integer('sticker_id')->nullable();
            $table->string('site_country')->nullable();
            $table->float('penalty')->nullable();
            $table->integer('additional_payment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wb_supplier_report_period');
    }
};
