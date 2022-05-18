<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'mst_product';

    protected $fillable = [
        'product_name',
        'product_image',
        'product_price',
        'is_sales',
        'description',
    ];
}
