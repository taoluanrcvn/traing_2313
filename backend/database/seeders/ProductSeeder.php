<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now(env('TIME_ZONE', 'Asia/Ho_Chi_Minh'))->format('Y-m-d H:i:s');
        for($i = 15 ; $i < 50 ; $i++) {
            $product_id = 'S0000000' . $i;
            DB::table('mst_product')->insert([
                'product_id' => $product_id,
                'product_name' => 'Sản phẩm ' . $i,
                'product_image' => 'storage/app/images/Capture.PNG',
                'product_price'=> 100,
                'is_sales' => 1,
                'description' => 'Đầy là sản phẩm ' . $i,
                'inventory' => $i,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
        }

    }
}
