<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = ['budget', 'date', 'campaign_id'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
