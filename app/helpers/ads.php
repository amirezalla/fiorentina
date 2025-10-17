<?php

use App\Support\AdDisplayPool;
use App\Models\Ad;
use Illuminate\Support\Facades\Storage;

if (! function_exists('ad_prime_slots')) {
    /**
     * Prime the per-request pool exactly once.
     * Builds the slot => ad_group_id map and allocates unique creatives.
     */
    function ad_prime_slots(): void
    {
        static $primed = false;
        if ($primed) return;

        $slotConst = [
            'p1' => Ad::GROUP_DBLOG_P1,
            'p2' => Ad::GROUP_DBLOG_P2,
            'p3' => Ad::GROUP_DBLOG_P3,
            'p4' => Ad::GROUP_DBLOG_P4,
            'p5' => Ad::GROUP_DBLOG_P5,
        ];

        $ads = Ad::query()
            ->where('type', Ad::TYPE_ANNUNCIO_IMMAGINE)
            ->where('status', 1)
            ->whereIn('group', array_values($slotConst))
            ->get()
            ->keyBy('group');

        $map = [];
        foreach ($slotConst as $key => $const) {
            $map[$key] = optional($ads->get($const))->ad_group_id;
        }

        app(AdDisplayPool::class)->allocateForSlots($map);
        app()->instance('ads.slot.map', $map);

        $primed = true;
    }
}

if (! function_exists('ad_slot_img')) {
    function ad_slot_img(string $slotKey): ?string
    {
        ad_prime_slots();

        /** @var AdDisplayPool $pool */
        $pool = app(AdDisplayPool::class);

        $img = $pool->getAllocatedForSlot($slotKey);
        if (! $img || ! $img->image_url) return null;

        return preg_match('~^https?://~i', $img->image_url)
            ? $img->image_url
            : Storage::disk('wasabi')->url(ltrim($img->image_url, '/'));
    }
}

if (! function_exists('ad_slot_href')) {
    function ad_slot_href(string $slotKey): ?string
    {
        ad_prime_slots();

        /** @var AdDisplayPool $pool */
        $pool = app(AdDisplayPool::class);

        $img = $pool->getAllocatedForSlot($slotKey);
        return $img && $img->target_url ? $img->target_url : '#';
    }
}
