<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');
        DB::table('mst_shop')->insert([
            [
                'shop_name' => 'Amazon',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'shop_name' => 'Yahoo',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'shop_name' => 'Rakuten',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]
        ]);
        for($i = 0 ; $i < 10 ; $i++) {
            DB::table('mst_users')->insert([
                'name' => 'Nguyên Văn ' . $i,
                'email' => 'nguyenvan' . $i .'@gmail.com',
                'password' => md5('nguyenvan' . $i),
                'group_role' => 'Editor',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
        }
        for($i = 10 ; $i < 20 ; $i++) {
            DB::table('mst_users')->insert([
                'name' => 'Nguyên Văn ' . $i,
                'email' => 'nguyenvan' . $i .'@gmail.com',
                'password' => md5('nguyenvan' . $i),
                'group_role' => 'Reviewer',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
        }
    }
}
