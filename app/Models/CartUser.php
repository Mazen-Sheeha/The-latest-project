<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartUser extends Model
{
    protected $fillable = [
        'full_name',
        'phone',
        'government',
        'address',
        'page_id',
        'offer_price',
        'quantity',
        'order_index_string'
    ];

    protected $hidden = ['order_index_string'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
