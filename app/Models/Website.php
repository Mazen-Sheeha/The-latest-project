<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Website extends Model
{
    protected $fillable = ['key', 'domain'];

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
