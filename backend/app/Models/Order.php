<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'mst_shop';
    public $timestamps = true;
    protected $fillable = [
        'order_shop',
        'customer_id',
        'total_price',
        'payment_method',
        'ship_charge',
        'tax',
        'order_date',
        'shipment_date',
        'shipment_status',
        'order_status',
        'cancel_date',
        'note_customer',
        'note_order',
        'error_code_api'
    ];
}
