<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVpOrderLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vp_order_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string("gtin");
            $table->string("sku");
            $table->string("name")->nullable();
            $table->double("price")->nullable();
            $table->double("taxRate")->nullable();
            $table->integer("quantity")->nullable();
            $table->string("brandName")->nullable();
            $table->double("totalPrice")->nullable();
            $table->string("manufacturerReference")->nullable();
            $table->boolean("in_stock")->nullable();
            $table->boolean("out_of_stock")->nullable();
            $table->integer("shipped")->nullable()->default(0);
            $table->integer("returned")->nullable()->default(0);
            $table->integer("cancelled")->nullable()->default(0);
            $table->integer("processing")->nullable()->default(0);
            $table->integer("pending")->nullable()->default(0);
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('vp_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vp_order_lines');
    }
}
