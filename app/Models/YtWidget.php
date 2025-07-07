<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YtWidget extends Model
{
    protected $fillable = ['type', 'live_url', 'playlist_urls'];

    protected $casts = [
        'playlist_urls' => 'array',   // <— auto-json (Laravel ≥ 10)
    ];
}
