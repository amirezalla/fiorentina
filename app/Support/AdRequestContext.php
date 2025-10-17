<?php

namespace App\Support;

use App\Models\Ad;
use App\Models\AdGroupImage;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AdRequestContext
{
    /** @var array<int,int> slotConst => ad_group_id */
    protected array $slotToGroup = [];

    /** @var array<int,AdGroupImage> group_id => allocated image */
    protected array $alloc = [];

    /** Map slots to their ad_group_id (from ads you loaded) */
    public function mapSlots(array $slotToGroupId): void
    {
        // only ints, no nulls
        $this->slotToGroup = collect($slotToGroupId)
            ->filter(fn ($gid) => $gid !== null)
            ->map(fn ($gid) => (int) $gid)
            ->all();
    }

    /** Store final allocation: group_id => AdGroupImage */
    public function setAllocation(array $groupIdToImage): void
    {
        $this->alloc = $groupIdToImage;
    }

    /** Get the allocated image model for a slot const (P1..P5) */
    public function getImageForSlot(int $slotConst): ?AdGroupImage
    {
        $gid = $this->slotToGroup[$slotConst] ?? null;
        if (!$gid) return null;

        return $this->alloc[$gid] ?? null;
    }

    /** Convenience: resolved image URL for a slot */
    public function img(int $slotConst): ?string
    {
        $img = $this->getImageForSlot($slotConst);
        if (!$img) return null;

        $key = $img->image_url;
        if (!$key) return null;

        return preg_match('~^https?://~i', $key)
            ? $key
            : \Storage::disk('wasabi')->url(ltrim($key, '/'));
    }

    /** Convenience: target href for a slot */
    public function href(int $slotConst): ?string
    {
        $img = $this->getImageForSlot($slotConst);
        return $img ? ($img->target_url ?: '#') : null;
    }
}
