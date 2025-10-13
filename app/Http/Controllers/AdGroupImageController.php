<?php

namespace App\Http\Controllers;

use App\Models\AdGroup;
use App\Models\AdGroupImage;
use Botble\Media\RvMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class AdGroupImageController extends Controller
{
    public function __construct(protected RvMedia $rvMedia) {}

    /** Normalize a target URL to your click tracker once (no double-wrap). */
    protected function wrapTracker(?string $url): ?string
    {
        if (!$url) return null;

        // already tracked?
        $trackerBase = url('/adsclicktracker');
        if (str_starts_with($url, $trackerBase)) {
            return $url;
        }
        return $trackerBase . '?url=' . urlencode($url);
    }

    public function store(Request $request, AdGroup $group)
    {
        $request->validate([
            'images'     => 'required|array|min:1',
            'images.*'   => 'file|mimes:jpeg,png,jpg,gif,bmp,webp|max:4096',
            'urls'       => 'nullable|array',
            'urls.*'     => 'nullable|url|max:1024',
            'expires'    => 'nullable|array',
            'expires.*'  => 'nullable|date',   // (optional) parallel expiry inputs by index
        ]);

        $width  = $group->width;
        $height = $group->height;

        $urls    = $request->input('urls', []);     // same order as files
        $expires = $request->input('expires', []);  // same order as files

        foreach ($request->file('images', []) as $i => $file) {
            if (!$file->isValid()) continue;

            $name = Str::random(32) . time() . '.' . $file->getClientOriginalExtension();

            // Resize if group has fixed size
            $img = ImageManager::gd()->read($file);
            if ($width && $height) {
                $img = $img->resize($width, $height);
            }

            $tmp = sys_get_temp_dir() . "/$name";
            $img->encode()->save($tmp);

            $up = $this->rvMedia->uploadFromPath($tmp, 0, 'ad-group-images/');
            @unlink($tmp);

            $storedUrl = $up['data']->url ?? null;
            if ($storedUrl) {
                AdGroupImage::create([
                    'group_id'   => $group->id,
                    'image_url'  => $storedUrl,
                    'target_url' => $this->wrapTracker($urls[$i] ?? null),
                    'expires_at' => $expires[$i] ?? null,
                    // views/clicks default to 0
                ]);
            }
        }

        return back()->with('success', 'Images uploaded.');
    }

    public function destroy(AdGroup $group, AdGroupImage $image)
    {
        abort_if((int) $image->group_id !== (int) $group->id, 403);
        $image->delete();
        return back()->with('success', 'Image removed.');
    }

    public function sort(Request $request, AdGroup $group)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer',
        ]);

        // If you use sort_order, persist here:
        // foreach ($request->order as $pos => $id) {
        //     AdGroupImage::where('group_id', $group->id)
        //         ->where('id', $id)
        //         ->update(['sort_order' => $pos + 1]);
        // }

        return response()->json(['ok' => true]);
    }

    /** Bulk update links + per-image expiry from the edit screen. */
    public function updateLinks(Request $request, AdGroup $group)
    {
        $data = $request->validate([
            'image_urls'    => 'required|array',
            'image_urls.*'  => 'nullable|url|max:1024',
            'image_expiry'  => 'nullable|array',
            'image_expiry.*'=> 'nullable|date',
        ]);

        $pairs   = $data['image_urls'];
        $expairs = $data['image_expiry'] ?? [];

        $images = AdGroupImage::where('group_id', $group->id)
            ->whereIn('id', array_keys($pairs))
            ->get();

        foreach ($images as $img) {
            $img->target_url = $this->wrapTracker($pairs[$img->id] ?: null);
            if (array_key_exists($img->id, $expairs)) {
                $img->expires_at = $expairs[$img->id] ?: null;
            }
            $img->save();
        }

        return back()->with('success', 'Image links & expiry updated.');
    }
}
