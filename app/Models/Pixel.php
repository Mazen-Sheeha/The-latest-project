<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pixel extends Model
{
    protected $table = 'pixels';

    protected $fillable = [
        'name',
        'type',
        'pixel_id',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'page_pixel', 'pixel_id', 'page_id')
            ->withTimestamps();
    }

    public static function getTypes(): array
    {
        return [
            'meta' => 'Meta (Facebook)',
            'google_ads' => 'Google Ads',
            'google_analytics' => 'Google Analytics',
            'tiktok' => 'TikTok',
            'snapchat' => 'Snapchat',
            'twitter' => 'Twitter (X)',
            'other' => 'أخرى',
        ];
    }
}
