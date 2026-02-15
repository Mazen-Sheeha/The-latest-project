<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Page extends Model
{
    protected $table = 'pages';

    protected $fillable = [
        'name',
        'product_id',
        'title',
        'slug',
        'theme_color',
        'items_sold_count',
        'reviews_count',
        'sale_percent',
        'original_price',
        'sale_price',
        'sale_ends_at',
        'images',
        'description',
        'is_active',
        'domain_id',
        'features',
        'whatsapp_phone',
        'offers',
        'meta_pixel',
        'tiktok_pixel',
        'snapchat_pixel',
        'twitter_pixel',
        'google_ads_pixel',
        'google_analytics',
    ];

    protected $casts = [
        'items_sold_count' => 'integer',
        'reviews_count' => 'integer',
        'sale_percent' => 'integer',
        'original_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'sale_ends_at' => 'date',
        'images' => 'array',
        'is_active' => 'boolean',
        'features' => 'array',
        'offers' => 'array',
    ];

    // public function getRouteKeyName()
    // {
    //     return 'slug';
    // }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PageReview::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function pageSaleActive(): bool
    {
        if ($this->sale_ends_at === null) {
            return false;
        }

        if ($this->sale_price === null) {
            return false;
        }

        return Carbon::parse($this->sale_ends_at)->isFuture();
    }

    public function upsellProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'page_upsell_products', 'page_id', 'product_id')
            ->withPivot(['name', 'image', 'price'])
            ->withTimestamps();
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
