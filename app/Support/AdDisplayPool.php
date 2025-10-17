<?php

namespace App\Support;

use App\Models\Ad; // adjust namespace to your Ad model
use Illuminate\Support\Collection;

class AdDisplayPool
{
    /** @var array<string, array<int>> group => [ad_ids already shown this request] */
    protected array $seenByGroup = [];

    /** @var array<string, \Illuminate\Support\Collection> Cached ads per group this request */
    protected array $cacheByGroup = [];

    /** If TRUE, also prevent duplicates by image_url across same group */
    protected bool $dedupeByImageUrl = false;

    public function __construct(bool $dedupeByImageUrl = false)
    {
        $this->dedupeByImageUrl = $dedupeByImageUrl;
    }

    /**
     * Return ONE ad for a group, weighted and not repeated within this request.
     */
    public function pickOne(string|int $group): ?Ad
    {
        $ads = $this->adsForGroup($group);

        if ($ads->isEmpty()) {
            return null;
        }

        // Exclude those already shown this request (by ID)
        $shownIds = $this->seenByGroup[$group] ?? [];
        $candidates = $ads->whereNotIn('id', $shownIds);

        if ($this->dedupeByImageUrl && !empty($shownIds)) {
            $shownImages = $ads->whereIn('id', $shownIds)->pluck('image')->filter()->all();
            if ($shownImages) {
                $candidates = $candidates->reject(function ($ad) use ($shownImages) {
                    return $ad->image && in_array($ad->image, $shownImages, true);
                });
            }
        }

        // If we exhausted all, reset (so rotation can continue)
        if ($candidates->isEmpty()) {
            $this->seenByGroup[$group] = [];
            $candidates = $ads;
        }

        $picked = $this->weightedPick($candidates);
        if ($picked) {
            $this->seenByGroup[$group][] = (int) $picked->id;
        }

        return $picked;
    }

    /**
     * Return up to $count unique ads for a group (useful for blocks that show many slots).
     */
    public function pickMany(string|int $group, int $count): Collection
    {
        $out = collect();
        for ($i = 0; $i < $count; $i++) {
            $ad = $this->pickOne($group);
            if (!$ad) break;
            $out->push($ad);
        }
        return $out;
    }

    /**
     * Cache ads list per request for the group (DB hit only once per request).
     */
    protected function adsForGroup(string|int $group): Collection
    {
        if (!array_key_exists($group, $this->cacheByGroup)) {
            $this->cacheByGroup[$group] = Ad::query()
                ->typeAnnuncioImmagine()
                ->where('group', $group)
                // Optionally pre-filter by status/dates if you have them:
                // ->where('status', 1)
                // ->where(function($q){
                //     $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                // })->where(function($q){
                //     $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
                // })
                ->get();
        }

        return $this->cacheByGroup[$group];
    }

    /**
     * Weighted random pick (default weight = 1 when null/0).
     */
    protected function weightedPick(Collection $ads): ?Ad
    {
        if ($ads->isEmpty()) return null;

        $total = 0;
        foreach ($ads as $ad) {
            $w = (int) ($ad->weight ?? 1);
            if ($w < 1) $w = 1; // enforce minimum
            $total += $w;
        }

        if ($total <= 0) return $ads->first();

        $r = random_int(1, $total);
        $acc = 0;

        foreach ($ads as $ad) {
            $w = (int) ($ad->weight ?? 1);
            if ($w < 1) $w = 1;
            $acc += $w;
            if ($r <= $acc) {
                return $ad;
            }
        }

        return $ads->first();
    }
}
