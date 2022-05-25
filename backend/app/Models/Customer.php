<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'mst_customer';
    public $timestamps = true;
    protected $fillable = [
        'customer_name',
        'email',
        'tel_num',
        'address',
        'is_active'
    ];
    protected $primaryKey = 'customer_id';

}
