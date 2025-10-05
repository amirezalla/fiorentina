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
        ]);

        $width  = $group->width;
        $height = $group->height;

        foreach ($request->file('images', []) as $file) {
            if (!$file->isValid()) continue;

            $name = Str::random(32) . time() . '.' . $file->getClientOriginalExtension();

            // Resize (optional: only if width/height set)
            $img = ImageManager::gd()->read($file);
            if ($width && $height) {
                $img = $img->resize($width, $height);
            }

            $tmp = sys_get_temp_dir() . "/$name";
            $img->encode()->save($tmp);

            // Upload via Botble Media to Wasabi (same as your Ads flow)
            $up = $this->rvMedia->uploadFromPath($tmp, 0, 'ad-group-images/');
            @unlink($tmp);

            $url = $up['data']->url ?? null;
            if ($url) {
                $group->images()->create(['image_url' => $url]);
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
}
