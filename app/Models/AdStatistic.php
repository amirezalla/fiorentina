<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdStatistic extends Model
{
    protected $fillable = ['ad_id', 'date', 'impressions', 'clicks'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public static function trackImpression($adId)
    {
        static::updateOrCreate(
            [
                'ad_id' => $adId,
                'date'  => now()->toDateString(),
            ],
            [
                // increments impressions by 1
                'impressions' => \DB::raw('impressions + 1'),
            ]
        );
    }

    public static function trackClick($adId)
    {
        static::updateOrCreate(
            [
                'ad_id' => $adId,
                'date'  => now()->toDateString(),
            ],
            [
                // increments clicks by 1
                'clicks' => \DB::raw('clicks + 1'),
            ]
        );
    }
}
