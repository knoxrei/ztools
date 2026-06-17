<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    protected $fillable = [
        'code',
        'original_url',
        'password',
        'expires_at',
        'max_clicks',
        'clicks_count',
        'is_burn_after_use',
        'cloak_title',
        'cloak_desc',
        'connection_type',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_burn_after_use' => 'boolean',
        'clicks_count' => 'integer',
        'max_clicks' => 'integer',
    ];
}
