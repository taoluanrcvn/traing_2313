<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now(env('TIME_ZONE', 'Asia/Ho_Chi_Minh'))->format('Y-m-d H:i:s');
        for($i = 0 ; $i < 10 ; $i++) {
            DB::table('mst_customer')->insert([
                'customer_name' => 'Customer ' . Str::random(1),
                'email' =>  'customer' . Str::random(1) . '@gmail.com',
                'tel_num' =>  '0382873322',
                'address' => $i + 1 . ' Ung Văn Khiêm, Phường 25, Quận Bình Thạnh, TP.HCM',
                'is_active' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
        }
    }
}
