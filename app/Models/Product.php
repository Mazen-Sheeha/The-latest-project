<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'image',
        'code',
        'price',
        'stock',
        'shipping_company_id',
    ];

    public function image()
    {
        $image = $this->image;
        if ($image)
            return asset($image);
        else
            return asset('images/productDefault.webp');
    }

    public function shipping_company()
    {
        return $this->belongsTo(ShippingCompany::class, 'shipping_company_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->withPivot(['price', 'quantity', 'real_price'])
            ->withTimestamps();
    }

    public function hasOrders()
    {
        return $this->hasMany(OrderProduct::class)->count() > 0;
    }
}
