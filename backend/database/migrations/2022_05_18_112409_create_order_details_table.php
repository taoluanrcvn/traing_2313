<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_order_detail', function (Blueprint $table) {
            $table->integer('order_id', false, true);
            $table->integer('detail_line', true);
            $table->string('product_id', 50);
            $table->integer('price_buy');
            $table->tinyInteger('shop_id', false, true);
            $table->integer('receiver_id');
            $table->timestamps();

            $table->index(['order_id']);
            $table->foreign('order_id')->references('order_id')->on('mst_order');
            $table->foreign('product_id')->references('product_id')->on('mst_product');
            $table->foreign('shop_id')->references('shop_id')->on('mst_shop');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_order_detail');
    }
}
