<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdGroupImage extends Model
{
    protected $fillable = [
        'group_id',
        'image_url',
        'target_url',
        'expires_at',
        'views',
        'clicks',
        'sort_order',
        'weight',
    ];

    protected $casts = [
        'expires_at' => 'date',   // or 'datetime' if you chose DATETIME
        'views'      => 'integer',
        'clicks'     => 'integer',
    ];

    public function group()
    {
        return $this->belongsTo(AdGroup::class, 'group_id');
    }

    /** Increment view counter (call this when the ad image is rendered). */
    public function bumpView(): void
    {
        $this->increment('views');
    }

    /** Increment click counter (call from your click-redirect controller). */
    public function bumpClick(): void
    {
        $this->increment('clicks');
    }
}
