<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YtWidget extends Model
{
    protected $fillable = ['type', 'live_url', 'playlist_urls'];

    protected $casts = [
        'playlist_urls' => 'array',   // <— auto-json (Laravel ≥ 10)
    ];
    public static function extractId(string $url): string
    {
        preg_match('%(?:youtu\\.be/|v=|embed/)([\\w-]{11})%i', $url, $m);
        return $m[1] ?? $url;
    }
}
