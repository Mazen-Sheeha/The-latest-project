<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageReview extends Model
{
    protected $table = 'page_reviews';
    protected $fillable = [
        'page_id',
        'reviewer_name',
        'comment',
        'stars',
        'reviewer_image',
    ];

    protected $casts = [
        'stars' => 'integer',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
