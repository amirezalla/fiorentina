<?php
// app/Support/AdDisplayPool.php

namespace App\Support;

use App\Models\AdGroupImage;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AdDisplayPool
{
    /** Cache per richiesta: groupId => Collection<AdGroupImage> */
    protected array $pool = [];

    /** Usati per richiesta: groupId => int[] (ad_group_image.id) */
    protected array $used = [];

    /**
     * Prende una creatività dal gruppo, rispettando weight
     * e senza ripetere nella stessa richiesta.
     */
    public function pick(int|string $groupId): ?AdGroupImage
    {
        $groupId = (string) $groupId;

        // carica pool una sola volta per gruppo
        if (! isset($this->pool[$groupId])) {
            $this->pool[$groupId] = $this->loadGroupImages($groupId);
        }

        /** @var Collection $images */
        $images = $this->pool[$groupId];
        if ($images->isEmpty()) {
            return null;
        }

        // escludi già usati, se esaurito resetta
        $usedIds = $this->used[$groupId] ?? [];
        $available = $images->reject(fn ($img) => in_array($img->id, $usedIds, true));
        if ($available->isEmpty()) {
            $this->used[$groupId] = [];
            $available = $images;
        }

        // estrazione pesata
        $chosen = $this->weightedPick($available);

        // marca come usato
        $this->used[$groupId][] = $chosen->id;

        return $chosen;
    }

    protected function loadGroupImages(string $groupId): Collection
    {
        return AdGroupImage::query()
            ->where('group_id', $groupId)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('sort_order')
            ->get(['id','group_id','image_url','target_url','weight']);
    }

    protected function weightedPick(Collection $images): AdGroupImage
    {
        $bag = [];
        foreach ($images as $img) {
            $w = max(1, (int) $img->weight);
            for ($i = 0; $i < $w; $i++) {
                $bag[] = $img->id;
            }
        }
        if (empty($bag)) {
            return $images->first();
        }
        $pickedId = Arr::random($bag);

        return $images->firstWhere('id', $pickedId) ?? $images->first();
    }
}
