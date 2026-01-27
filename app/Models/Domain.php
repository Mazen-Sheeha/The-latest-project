<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domain extends Model
{
    protected $table = 'domains';

    protected $fillable = [
        'domain',
        'status',
        'verification_ip',
        'setup_type',
        'dns_record'
    ];

    protected $casts = [
        'dns_record' => 'array',
    ];
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
