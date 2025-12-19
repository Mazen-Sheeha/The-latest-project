<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_product';

    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'code',
        'quantity',
        'real_price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
