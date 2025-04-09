<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\AdStatistic;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Botble\Media\RvMedia;

class AdController extends BaseController
{
    public function __construct(protected RvMedia $rvMedia)
    {
    }

    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()->add("Advertisements");
    }
    public function index(Request $request)
    {
        $this->pageTitle("Ads List");
    
        $ads = Ad::query()
            // 1) add the sums for impressions & clicks:
            ->withSum('adStatistics as total_impressions', 'impressions')
            ->withSum('adStatistics as total_clicks', 'clicks')
            
            // 2) apply the existing filters
            ->where(function ($q) use ($request) {
                $q->when(
                    $request->filled('group') && in_array($request->group, array_keys(Ad::GROUPS)),
                    fn($q) => $q->where('group', $request->group)
                )
                ->when(
                    $request->filled('q'),
                    fn($q) => $q->where('title', 'LIKE', '%' . $request->q . '%')
                )
                ->when(
                    $request->filled('status') && in_array(intval($request->status), [1, 2]),
                    function ($q) use ($request) {
                        $request->status == 1
                            ? $q->where('status', 1)
                            : $q->where('status', 0);
                    }
                );
            })
            ->latest()
            ->paginate(20);
    
        return view('ads.view', compact('ads'));
    }

    public function create()
    {
        $this->pageTitle("Create Ad");
        return view('ads.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'post_title' => 'required|string|max:255',
            'width'      => 'nullable|numeric|min:0',
            'height'     => 'nullable|numeric|min:0',
            'weight'     => 'required|numeric|min:1',
            'type'       => ['required', Rule::in(array_keys(Ad::TYPES))],
            'group'      => ['required', Rule::in(array_keys(Ad::GROUPS))],
        ]);

        $advertisement = new Ad();
        $advertisement->title  = $validated['post_title'];
        $advertisement->group  = $request->group;
        $advertisement->weight = $request->weight;
        $advertisement->type   = $request->type;
        $advertisement->width  = $request->width;
        $advertisement->height = $request->height;
        $advertisement->url    = $request->url;
        $advertisement->amp    = $request->amp;

        // Handle file upload if type=1 (image ad)
        if ($advertisement->type == 1) {
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                foreach ($files as $file) {
                    if ($file->isValid()) {
                        // Generate a unique file name using a random string and the current timestamp.
                        $filename = Str::random(32) . time() . "." . $file->getClientOriginalExtension();
            
                        // Read and optionally resize the image.
                        $imageResized = ImageManager::gd()->read($file);
                        if ($request->width && $request->height) {
                            $imageResized = $imageResized->resize($request->width, $request->height);
                        }
                        $imageResized = $imageResized->encode();
            
                        // Save the processed image to a temporary path.
                        $tempPath = sys_get_temp_dir() . '/' . $filename;
                        file_put_contents($tempPath, $imageResized);
            
                        // Upload the image via RvMedia to the 'ads-images/' folder.
                        $uploadResult = $this->rvMedia->uploadFromPath($tempPath, 0, 'ads-images/');
            
                        // Remove the temporary file.
                        unlink($tempPath);
            
                        // Create an associated AdImage record for this ad.
                        $ad->images()->create(['image_url' => $uploadResult['data']->url]);
                    }
                }
            }
            
        } else {
            // For type=2 (google manager or custom script) we store the script/amp in 'image' field if you prefer
            $advertisement->image = $request->image;
        }

        try {
            $advertisement->save();
            return redirect()->route('ads.index')->with('success', 'Advertisement created successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to save advertisement: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to save advertisement. Please try again.');
        }
    }

    public function edit(Ad $ad)
    {
        return view('ads.edit', compact('ad'));
    }

    public function update(Request $request, Ad $ad)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'post_title' => 'required|string|max:255',
            'width'      => 'nullable|numeric|min:0',
            'height'     => 'nullable|numeric|min:0',
            'weight'     => 'required|numeric|min:1',
            'type'       => ['required', Rule::in(array_keys(Ad::TYPES))],
            'group'      => ['required', Rule::in(array_keys(Ad::GROUPS))],
        ]);

        $status = $request->status == '1' ? 1 : 0;

        $data = [
            'title'  => $request->post_title,
            'group'  => $request->group,
            'type'   => $request->type,
            'width'  => $request->width,
            'height' => $request->height,
            'url'    => $request->url,
            'weight' => $request->weight,
            'amp'    => $request->amp ?? null,
            'status' => $status,
        ];

        if ($data['type'] == 1) {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $filename     = Str::random(32) . time() . "." . $request->file('image')->getClientOriginalExtension();
                $imageResized = ImageManager::gd()->read($request->image);

                if ($request->width && $request->height) {
                    $imageResized = $imageResized->resize($request->width, $request->height);
                }
                $imageResized = $imageResized->encode();
                $path = "ads-images/" . $filename;
                Storage::disk('public')->put($path, $imageResized);
                $data['image'] = $path;
            }
        } else {
            $data['image'] = $request->image;
        }

        try {
            $ad->update($data);
            $ad->refresh();
            return redirect()->route('ads.index')->with('success', 'Advertisement updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to save advertisement: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to save advertisement. Please try again.');
        }
    }

    public function destroy(Ad $ad)
    {
        $ad->delete();
        return redirect()->route('ads.index')->with('success', 'Ad deleted successfully.');
    }

    public function trackClick($id)
    {
        $ad = Ad::findOrFail($id);
        // increment clicks for the given ad
        AdStatistic::trackClick($ad->id);

        // redirect to the ad’s URL
        return redirect()->to($ad->url);
    }
}
