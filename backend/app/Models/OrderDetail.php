<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'mst_order_detail';

    protected $fillable = [
        'detail_line',
        'price_buy',
        'shop_id',
        'receiver_id'
    ];
}
