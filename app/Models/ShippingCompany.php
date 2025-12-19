<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCompany extends Model
{
    protected $fillable = ['name', 'price'];

    public function products()
    {
        return $this->hasMany(Product::class, 'shipping_company_id');
    }
}
