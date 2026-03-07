<?php

namespace App\Models;

use App\Enums\CartUserStatusEnum;
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
        'order_index_string',
        'utm_source',
        'utm_campaign',
        'order_id',
        'status',
    ];

    protected $hidden = ['order_index_string'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getRowColorAttribute(): string
    {
        return match ($this->status) {
            CartUserStatusEnum::COMPLETED->value => '#39BF52',
            CartUserStatusEnum::CANCELED->value => '#BF3939',
            default => '#FFFFFF',
        };
    }
}
