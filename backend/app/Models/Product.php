<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'mst_product';
    protected $fillable = [
        'product_id',
        'product_name',
        'product_image',
        'product_price',
        'is_sales',
        'inventory',
        'description',
    ];
    public $incrementing = FALSE;
    protected $primaryKey = 'product_id';

}
