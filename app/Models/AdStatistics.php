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
        self::updateOrCreate(
            ['ad_id' => $adId, 'date' => now()->toDateString()],
            ['impressions' => \DB::raw('impressions + 1')]
        );
    }

    public static function trackClick($adId)
    {
        self::updateOrCreate(
            ['ad_id' => $adId, 'date' => now()->toDateString()],
            ['clicks' => \DB::raw('clicks + 1')]
        );
    }
}