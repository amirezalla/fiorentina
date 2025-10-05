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

    public function store(Request $request, AdGroup $group)
    {
        $request->validate([
        'images'   => 'required|array|min:1',
        'images.*' => 'file|mimes:jpeg,png,jpg,gif,bmp|max:4096',
        'urls'     => 'nullable|array',
        'urls.*'   => 'nullable|url|max:1024',
    ]);

    $width  = $group->width;
    $height = $group->height;

    $urls = $request->input('urls', []); // same order as files

    foreach ($request->file('images', []) as $i => $file) {
        if (!$file->isValid()) continue;

        $name = \Illuminate\Support\Str::random(32) . time() . '.' . $file->getClientOriginalExtension();

        $img = \Intervention\Image\ImageManager::gd()->read($file);
        if ($width && $height) $img = $img->resize($width, $height);

        $tmp = sys_get_temp_dir() . "/$name";
        $img->encode()->save($tmp);

        $up = $this->rvMedia->uploadFromPath($tmp, 0, 'ad-group-images/');
        @unlink($tmp);

        $url = $up['data']->url ?? null;
        if ($url) {
            $group->images()->create([
                'image_url'  => $url,
                'target_url' => $urls[$i] ?? null,   // ← pair file i with url i
            ]);
        }
    }

    return back()->with('success','Images uploaded.');
    }

    public function destroy(AdGroup $group, AdGroupImage $image)
    {
        // Extra safety: ensure image belongs to group
        if ((int)$image->group_id !== (int)$group->id) {
            abort(403);
        }
        $image->delete();
        return back()->with('success','Image removed.');
    }

    public function sort(Request $request, AdGroup $group)
    {
        // (Optional) If you add a "position" column later, you can persist it here.
        // For now we just accept and return OK to keep UI happy.
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        // Example (uncomment if you add `position` to ad_group_images):
        // foreach ($request->input('order') as $pos => $id) {
        //     AdGroupImage::where('group_id',$group->id)->where('id',$id)->update(['position' => $pos+1]);
        // }

        return response()->json(['ok' => true]);
    }

    public function updateLinks(Request $request, AdGroup $group)
{
    $data = $request->validate([
        'image_urls'   => 'required|array',
        'image_urls.*' => 'nullable|url|max:1024',
    ]);

    $pairs = $data['image_urls']; // [image_id => url]

    // Only update images in this group
    $images = AdGroupImage::where('group_id', $group->id)
                ->whereIn('id', array_keys($pairs))
                ->get();

    foreach ($images as $img) {
        $img->target_url = $pairs[$img->id] ?: null;
        $img->save();
    }

    return back()->with('success', 'Image links updated.');
}

}
