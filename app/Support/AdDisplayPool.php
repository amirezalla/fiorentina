<?php
// app/Support/AdDisplayPool.php
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

    /** allocations keyed by slot key ('p1','p2',...) */
    protected array $allocatedBySlot  = []; // slot => AdGroupImage|null

    /** used creatives in this request (avoid dupes) */
    protected array $usedKeys = [];         // uniqKey => true

    /* -------- GROUP-BASED API (used by model helpers) -------- */
    public function allocateUnique(array $groupIds): void
    {
        foreach ($groupIds as $gid) {
            $gid = (int) $gid;
            if ($gid <= 0) continue;

            if (array_key_exists($gid, $this->allocatedByGroup)) {
                continue;
            }

            $images = $this->groupCache[$gid] ??= $this->loadGroupImages($gid);

            $choice = $this->weightedPickAvoidingUsed($images)
                   ?? $this->weightedPick($images);

            $this->allocatedByGroup[$gid] = $choice;
            if ($choice) {
                $this->usedKeys[$this->uniqKey($choice)] = true;
            }
        }
    }

    public function getAllocated(int $groupId): ?AdGroupImage
    {
        if (! array_key_exists($groupId, $this->allocatedByGroup)) {
            $this->allocateUnique([$groupId]); // lazy
        }
        return $this->allocatedByGroup[$groupId] ?? null;
    }

    /* -------- SLOT-BASED API (used by your includes/helpers) -------- */
    public function allocateForSlots(array $slotToGroupId): void
    {
        foreach ($slotToGroupId as $slot => $gid) {
            if (array_key_exists($slot, $this->allocatedBySlot)) continue;

            $gid = (int) $gid;
            if ($gid <= 0) { $this->allocatedBySlot[$slot] = null; continue; }

            $images = $this->groupCache[$gid] ??= $this->loadGroupImages($gid);

            $choice = $this->weightedPickAvoidingUsed($images)
                   ?? $this->weightedPick($images);

            $this->allocatedBySlot[$slot] = $choice;
            if ($choice) {
                $this->usedKeys[$this->uniqKey($choice)] = true;
            }
        }
    }

    public function getAllocatedForSlot(string $slot): ?AdGroupImage
    {
        return $this->allocatedBySlot[$slot] ?? null;
    }

    /* ---------------- internals ---------------- */
protected function loadGroupImages(int $groupId): Collection
{
    return AdGroupImage::query()
        ->where('group_id', $groupId)
        ->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhereRaw("CAST(expires_at AS CHAR) = ''")
              ->orWhere('expires_at', '>', now());
        })
        ->orderBy('sort_order')
        ->get(['id', 'group_id', 'image_url', 'target_url', 'weight']);
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
