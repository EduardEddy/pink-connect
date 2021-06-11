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
            $table->integer("price")->nullable();
            $table->integer("taxRate")->nullable();
            $table->integer("quantity")->nullable();
            $table->string("brandName")->nullable();
            $table->integer("totalPrice")->nullable();
            $table->string("manufacturerReference")->nullable();
            $table->enum('status', ['RETURNED','PENDING', 'PROCESSING', 'SHIPPED','CANCELLED'])->nullable();          
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
