<?php

namespace App\Support;

use App\Models\AdGroupImage;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AdDisplayPool
{
    /** cache: groupId => Collection<AdGroupImage> */
    protected array $groupCache = [];

    /** allocations keyed by group id */
    protected array $allocatedByGroup = []; // gid => AdGroupImage|null

    /** allocations keyed by slot (e.g. 'p1','p2',...) */
    protected array $allocatedBySlot  = []; // slot => AdGroupImage|null

    /** set of already-used creatives (avoid dupes within a request) */
    protected array $usedKeys = [];         // uniqKey => true

    /* -------------------------------------------------------------
     |  A) GROUP-based API  (what your model uses)
     * ------------------------------------------------------------*/
    public function allocateUnique(array $groupIds): void
    {
        foreach ($groupIds as $gid) {
            $gid = (int) $gid;
            if ($gid <= 0) continue;

            if (array_key_exists($gid, $this->allocatedByGroup)) {
                continue; // already allocated
            }

            $images = $this->groupCache[$gid] ??= $this->loadGroupImages($gid);

            // try weight-aware pick avoiding used creatives
            $choice = $this->weightedPickAvoidingUsed($images);
            if (!$choice) {
                // fallback: any (still weighted)
                $choice = $this->weightedPick($images);
            }

            $this->allocatedByGroup[$gid] = $choice;
            if ($choice) {
                $this->usedKeys[$this->uniqKey($choice)] = true;
            }
        }
    }

    public function getAllocated(int $groupId): ?AdGroupImage
    {
        if (!array_key_exists($groupId, $this->allocatedByGroup)) {
            $this->allocateUnique([$groupId]); // lazy allocate
        }
        return $this->allocatedByGroup[$groupId] ?? null;
    }

    /* -------------------------------------------------------------
     |  B) SLOT-based API  (what your view composers may use)
     * ------------------------------------------------------------*/
    public function allocateForSlots(array $slotToGroupId): void
    {
        // ensure group allocations exist first (weight-aware & unique)
        $gids = array_values(array_unique(array_filter(array_map('intval', $slotToGroupId))));
        $this->allocateUnique($gids);

        foreach ($slotToGroupId as $slot => $gid) {
            $gid = (int) $gid;
            if (!$gid) { $this->allocatedBySlot[$slot] = null; continue; }
            if (array_key_exists($slot, $this->allocatedBySlot)) continue;

            $this->allocatedBySlot[$slot] = $this->allocatedByGroup[$gid] ?? null;
        }
    }

    public function getAllocatedForSlot(string $slot): ?AdGroupImage
    {
        return $this->allocatedBySlot[$slot] ?? null;
    }

    /* -------------------------------------------------------------
     |  Internals
     * ------------------------------------------------------------*/
    protected function loadGroupImages(int $groupId): Collection
    {
        return AdGroupImage::query()
            ->where('group_id', $groupId)
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
            $w = max(1, (int) ($img->weight ?? 1));
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

            $w = max(1, (int) ($img->weight ?? 1));
            for ($i = 0; $i < $w; $i++) $bag[] = $img->id;
        }
        if (!$bag) return null;

        $pickedId = Arr::random($bag);
        return $images->firstWhere('id', $pickedId);
    }
}
