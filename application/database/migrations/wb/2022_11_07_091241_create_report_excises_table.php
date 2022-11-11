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
        Schema::create('wb_supplier_report_excise', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->bigInteger('report_id')->nullable();
            $table->integer('finished_price')->nullable();
            $table->integer('operation_type_id')->nullable();
            $table->dateTime('fiscal_dt')->nullable();
            $table->integer('doc_number')->nullable();
            $table->string('fn_number')->nullable();
            $table->string('reg_number')->nullable();
            $table->string('excise')->nullable();
            $table->dateTime('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wb_supplier_report_excise');
    }
};
