<?php
// app/Support/AdDisplayPool.php

namespace App\Support;

use App\Models\AdGroupImage;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AdDisplayPool
{
    /** groupId => Collection<AdGroupImage> */
    protected array $pool = [];

    /** groupId => AdGroupImage|null (final allocation for this request) */
    protected array $allocated = [];

    /** creatives already used in this request (by a global unique key) */
    protected array $usedKeys = [];

    /**
     * Allocate unique creatives across the given groups (once per request).
     * Safe to call multiple times; it won’t re-allocate already filled groups.
     */
    public function allocateUnique(array $groupIds): void
    {
        foreach ($groupIds as $gid) {
            $gid = (string) $gid;

            if (array_key_exists($gid, $this->allocated)) {
                continue; // already allocated for this request
            }
            if (! isset($this->pool[$gid])) {
                $this->pool[$gid] = $this->loadGroupImages($gid);
            }

            $images = $this->pool[$gid];
            if ($images->isEmpty()) {
                $this->allocated[$gid] = null;
                continue;
            }

            // try to pick a creative (respecting weight) that isn’t used yet
            $choice = $this->weightedPickAvoidingUsed($images);

            // mark used (by a unique key; here: image_url if present, fallback id)
            if ($choice) {
                $this->allocated[$gid] = $choice;
                $this->usedKeys[$this->uniqKey($choice)] = true;
            } else {
                // fallback: just pick something
                $this->allocated[$gid] = $this->weightedPick($images);
            }
        }
    }

    /** Get allocation for one group; allocate lazily if not done. */
    public function getAllocated(int|string $groupId): ?AdGroupImage
    {
        $groupId = (string) $groupId;

        if (! array_key_exists($groupId, $this->allocated)) {
            $this->allocateUnique([$groupId]);
        }

        return $this->allocated[$groupId] ?? null;
    }

    /** ---------- internals ---------- */

    protected function loadGroupImages(string $groupId): Collection
    {
        return AdGroupImage::query()
            ->where('group_id', $groupId)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('sort_order')
            ->get(['id', 'group_id', 'image_url', 'target_url', 'weight']);
    }

    protected function uniqKey(AdGroupImage $img): string
    {
        // avoid duplicates by file; if missing, fallback to id
        return $img->image_url ? ('url:' . $img->image_url) : ('id:' . $img->id);
    }

    /** Returns an image weighted by "weight". */
    protected function weightedPick(Collection $images): ?AdGroupImage
    {
        if ($images->isEmpty()) return null;

        $bag = [];
        foreach ($images as $img) {
            $w = max(1, (int) $img->weight);
            for ($i = 0; $i < $w; $i++) {
                $bag[] = $img->id;
            }
        }
        if (!$bag) return $images->first();

        $pickedId = Arr::random($bag);
        return $images->firstWhere('id', $pickedId) ?? $images->first();
    }

    /** Weighted pick that tries to avoid already used creatives. */
    protected function weightedPickAvoidingUsed(Collection $images): ?AdGroupImage
    {
        // Build a weighted bag but skip already used uniq-keys
        $bag = [];
        foreach ($images as $img) {
            $k = $this->uniqKey($img);
            if (isset($this->usedKeys[$k])) continue;

            $w = max(1, (int) $img->weight);
            for ($i = 0; $i < $w; $i++) {
                $bag[] = $img->id;
            }
        }

        if (! $bag) {
            // all were used; signal caller to fallback
            return null;
        }

        $pickedId = Arr::random($bag);
        return $images->firstWhere('id', $pickedId) ?? null;
    }
}
