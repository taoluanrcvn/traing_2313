<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_product', function (Blueprint $table) {
            $table->string('product_id', 20)->primary()->unique('product_id_index');
            $table->string('product_name', 255);
            $table->string('product_image', 255)->nullable();
            $table->decimal('product_price')->default(0);
            $table->boolean('is_sales')->default(1);
            $table->increments('inventory')->default(10);
            $table->longText('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_product');
    }
}
