<?php
// app/Support/AdDisplayPool.php

namespace App\Support;

use App\Models\AdGroupImage;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AdDisplayPool
{
    /** cache of images per group: gid => Collection<AdGroupImage> */
    protected array $groupCache = [];

    /** final allocation per slot key (e.g. 'p1','p2'...): slot => AdGroupImage|null */
    protected array $slotAlloc = [];

    /** creatives already used in this request (avoid duplicates globally) */
    protected array $usedKeys = [];

    /**
     * Allocate creatives per slot from each slot's group (weight-aware, non-repeating when possible).
     * Example $slotToGroupId: ['p1' => 4, 'p2' => 4, 'p3' => 6, 'p4' => null, 'p5' => 7]
     */
    public function allocateForSlots(array $slotToGroupId): void
    {
        foreach ($slotToGroupId as $slot => $gid) {
            if (!$gid) { $this->slotAlloc[$slot] = null; continue; }
            if (array_key_exists($slot, $this->slotAlloc)) continue;

            $images = $this->groupCache[$gid] ??= $this->loadGroupImages((int)$gid);

            // try to pick an unused creative respecting weight
            $choice = $this->weightedPickAvoidingUsed($images);

            // fallback: pick any (still weighted) if all have been used
            if (!$choice) $choice = $this->weightedPick($images);

            $this->slotAlloc[$slot] = $choice;
            if ($choice) $this->usedKeys[$this->uniqKey($choice)] = true;
        }
    }

    /** Getter used by your views/includes */
    public function getAllocatedForSlot(string $slot): ?AdGroupImage
    {
        return $this->slotAlloc[$slot] ?? null;
    }

    /** Optional alias if you ever need to read by group id directly */
    public function getAllocatedByGroupId(int $groupId): ?AdGroupImage
    {
        // find first slot that used this group
        foreach ($this->slotAlloc as $slot => $img) {
            if ($img && (int)$img->group_id === (int)$groupId) {
                return $img;
            }
        }
        return null;
    }

    // ---------- internals ----------

    protected function loadGroupImages(int $groupId): Collection
    {
        return AdGroupImage::query()
            ->where('group_id', $groupId)
            // include NULL, empty string, or future expiry
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '')
                  ->orWhere('expires_at', '>', now());
            })
            ->orderBy('sort_order')
            ->get(['id','group_id','image_url','target_url','weight']);
    }

    protected function uniqKey(AdGroupImage $img): string
    {
        return $img->image_url ? ('url:' . $img->image_url) : ('id:' . $img->id);
    }

    protected function weightedPick(Collection $images): ?AdGroupImage
    {
        if ($images->isEmpty()) return null;

        $bag = [];
        foreach ($images as $img) {
            $w = max(1, (int)($img->weight ?? 1));
            for ($i = 0; $i < $w; $i++) $bag[] = $img->id;
        }
        if (!$bag) return $images->first();

        $pickedId = Arr::random($bag);
        return $images->firstWhere('id', $pickedId) ?? $images->first();
    }

    protected function weightedPickAvoidingUsed(Collection $images): ?AdGroupImage
    {
        $bag = [];
        foreach ($images as $img) {
            $k = $this->uniqKey($img);
            if (isset($this->usedKeys[$k])) continue;

            $w = max(1, (int)($img->weight ?? 1));
            for ($i = 0; $i < $w; $i++) $bag[] = $img->id;
        }
        if (!$bag) return null;

        $pickedId = Arr::random($bag);
        return $images->firstWhere('id', $pickedId);
    }
}
