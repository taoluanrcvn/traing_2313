<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_order', function (Blueprint $table) {
            $table->increments('order_id')->unique('order_id_index');
            $table->string('order_shop', 40);
            $table->integer('customer_id',false, true);
            $table->integer('total_price');
            $table->tinyInteger('payment_method');
            $table->integer('ship_charge');
            $table->integer('tax');
            $table->dateTime('order_date');
            $table->dateTime('shipment_date');
            $table->boolean('shipment_status');
            $table->boolean('order_status');
            $table->dateTime('cancel_date');
            $table->string('note_customer', 255)->nullable();
            $table->string('note_order', 255)->nullable();
            $table->string('error_code_api', 200)->nullable();
            $table->timestamps();
            $table->foreign('customer_id')->references('customer_id')->on('mst_customer');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_order');

    }
}
