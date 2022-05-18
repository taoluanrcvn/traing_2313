<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    protected $table = 'mst_shop';
    public $timestamps = true;
    protected $fillable = [
        'shop_name'
    ];
}
